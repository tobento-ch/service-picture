<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Picture;

use League\MimeTypeDetection\FinfoMimeTypeDetector;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;
use Tobento\Service\Imager\Action;
use Tobento\Service\Imager\ActionCreateException;
use Tobento\Service\Imager\ActionInterface;
use Tobento\Service\Imager\ActionFactory;
use Tobento\Service\Imager\ActionFactoryInterface;
use Tobento\Service\Imager\ImageFormats;
use Tobento\Service\Imager\ImagerException;
use Tobento\Service\Imager\ImagerInterface;
use Tobento\Service\Imager\ResourceInterface;
use Tobento\Service\Imager\Resource;
use Tobento\Service\Picture\Exception\PictureCreateException;
use Tobento\Service\Picture\Exception\ResourceSizeException;
use Tobento\Service\Picture\Img;
use Tobento\Service\Picture\ImgInterface;
use Tobento\Service\Picture\PictureInterface;
use Tobento\Service\Picture\Source;
use Tobento\Service\Picture\Sources;
use Tobento\Service\Picture\SourcesInterface;
use Tobento\Service\Picture\Src;
use Tobento\Service\Picture\SrcInterface;
use Tobento\Service\Picture\Srcset;

/**
 * PictureCreator
 */
class PictureCreator implements PictureCreatorInterface
{
    /**
     * @var ActionFactoryInterface
     */
    protected ActionFactoryInterface $actionFactory;
    
    /**
     * Create a new PictureCreator.
     *
     * @param ImagerInterface $imager
     * @param array<array-key, class-string> $disallowedActions
     * @param array<array-key, string> $supportedMimeTypes
     * @param null|float $upsize
     * @param bool $skipSmallerSizedSrc
     * @param bool $verifySizes
     * @param null|ActionFactoryInterface $actionFactory
     * @param null|LoggerInterface $logger
     */
    public function __construct(
        protected ImagerInterface $imager,
        protected array $disallowedActions = [],
        protected array $supportedMimeTypes = ['image/png', 'image/jpeg', 'image/gif', 'image/webp'],
        protected null|float $upsize = null,
        protected bool $skipSmallerSizedSrc = false,
        protected bool $verifySizes = false,
        null|ActionFactoryInterface $actionFactory = null,
        protected null|LoggerInterface $logger = null,
    ) {
        $this->actionFactory = $actionFactory ?: new ActionFactory();
        
        if ($skipSmallerSizedSrc && $upsize < 1) {
            $this->upsize = 1.0;
        }
    }

    /**
     * Create a new picture from the given resource and definition.
     *
     * @param ResourceInterface $resource
     * @param DefinitionInterface $definition
     * @return CreatedPictureInterface
     * @throws PictureCreateException
     */
    public function createFromResource(ResourceInterface $resource, DefinitionInterface $definition): CreatedPictureInterface
    {
        return $this->createPicture($resource, $definition);
    }
    
    /**
     * Create a new picture from the given stream and definition.
     *
     * @param StreamInterface $stream
     * @param DefinitionInterface $definition
     * @return CreatedPictureInterface
     * @throws PictureCreateException
     */
    public function createFromStream(StreamInterface $stream, DefinitionInterface $definition): CreatedPictureInterface
    {
        return $this->createPicture(new Resource\Stream($stream), $definition);
    }

    /**
     * Create a new picture from the given resource and definition.
     *
     * @param ResourceInterface $resource
     * @param DefinitionInterface $definition
     * @return CreatedPictureInterface
     * @throws PictureCreateException
     */
    protected function createPicture(ResourceInterface $resource, DefinitionInterface $definition): CreatedPictureInterface
    {
        $picture = $definition->toPicture();
        
        $mimeType = $this->verifyMimeType($resource);
        
        if ($this->verifySizes) {
            $this->verifySizes($resource, $picture);
        }
        
        return new CreatedPicture(
            img: $this->createImg($resource, $picture, $mimeType),
            sources: $this->createSources($resource, $picture, $mimeType),
            attributes: $picture->attributes(),
            options: $picture->options(),
        );
    }
    
    /**
     * Create a new picture from the given resource and definition.
     *
     * @param ResourceInterface $resource
     * @param PictureInterface $picture
     * @param string $resourceMimeType
     * @return ImgInterface
     * @throws PictureCreateException
     */
    protected function createImg(
        ResourceInterface $resource,
        PictureInterface $picture,
        string $resourceMimeType
    ): ImgInterface {
        // handle src:
        $imgSrc = $this->modifySrc($picture->img()->src(), $picture, $resourceMimeType);
        $imgSrc = $this->createEncodedSrc($resource, $imgSrc);
        
        if ($this->skipEncodedSrc(src: $imgSrc, log: false)) {
            $this->logger?->log('debug', 'Img src larger as original');
        }
        
        $img = $picture->img()->withSrc($imgSrc);
        
        // handle srcset:
        if ($picture->img()->srcset()) {
            $srces = [];
            
            foreach($picture->img()->srcset() as $src) {
                $src = $this->modifySrc($src, $picture, $resourceMimeType);
                $src = $this->createEncodedSrc($resource, $src);
                
                if (! $this->skipEncodedSrc($src)) {
                    $srces[] = $src;
                }
            }
            
            $img = $img->withSrcset(new Srcset(...$srces));
        }
        
        return $img;
    }
    
    /**
     * Create a new picture from the given resource and definition.
     *
     * @param ResourceInterface $resource
     * @param PictureInterface $picture
     * @param string $resourceMimeType
     * @return SourcesInterface
     * @throws PictureCreateException
     */
    protected function createSources(
        ResourceInterface $resource,
        PictureInterface $picture,
        string $resourceMimeType
    ): SourcesInterface {
        $sources = [];
        
        foreach($picture->sources() as $sourceIndex => $source) {
            
            $srces = [];
            $type = null;
            
            // handle type attribute:
            if (isset($source->attributes()['type'])) {
                if ($this->isSupportedMimeType($source->attributes()['type'])) {
                    $type = $source->attributes()['type'];
                } else {
                    $this->logger?->log(
                        'debug',
                        sprintf('Skipped source %d as unsupported mime type', $sourceIndex),
                    );
                    
                    continue;
                }
            }
            
            foreach($source->srcset() as $src) {
                
                if ($type) {
                    $src = $src->withMimeType($type);
                }
                
                $src = $this->modifySrc($src, $picture, $resourceMimeType);
                $src = $this->createEncodedSrc($resource, $src);
                
                if (! $this->skipEncodedSrc($src)) {
                    $srces[] = $src;
                }
            }
            
            $sources[] = $source->withSrcset(new Srcset(...$srces));
        }
        
        return new Sources(...$sources);
    }
    
    /**
     * Modify src.
     *
     * @param SrcInterface $src
     * @param PictureInterface $picture
     * @param $resourceMimeType string
     * @return SrcInterface
     */
    protected function modifySrc(
        SrcInterface $src,
        PictureInterface $picture,
        string $resourceMimeType
    ): SrcInterface {
        // determine mime type:
        if (is_null($src->mimeType())) {
            // convert mime types:
            if (
                isset($picture->options()['convert'][$resourceMimeType])
                && $this->isSupportedMimeType($picture->options()['convert'][$resourceMimeType])
            ) {
                $src = $src->withMimeType($picture->options()['convert'][$resourceMimeType]);
            } else {
                $src = $src->withMimeType($resourceMimeType);
            }
        }
        
        // verify mime type:
        if (! $this->isSupportedMimeType($src->mimeType())) {
            $src = $src->withMimeType($resourceMimeType);
        }
        
        // determine quality:
        if (
            !isset($src->options()['quality'])
            && isset($picture->options()['quality'])
            && is_array($picture->options()['quality'])
        ) {
            $quality = $picture->options()['quality'][$src->mimeType()] ?? null;
            
            $src = $src->withOptions(array_merge($src->options(), ['quality' => $quality]));
        }
        
        // determine actions:
        if (
            !isset($src->options()['actions'])
            && isset($picture->options()['actions'])
            && is_array($picture->options()['actions'])
        ) {
            $src = $src->withOptions(array_merge(
                $src->options(),
                ['actions' => $picture->options()['actions']]
            ));
        }
        
        return $src;
    }

    /**
     * Create a new picture from the given resource and definition.
     *
     * @param ResourceInterface $resource
     * @param SrcInterface $src
     * @return SrcInterface
     * @throws PictureCreateException
     */
    protected function createEncodedSrc(ResourceInterface $resource, SrcInterface $src): SrcInterface
    {
        if (is_null($src->mimeType())) {
            throw new PictureCreateException(resource: $resource, message: 'No mime type specified');
        }
        
        $mimeType = $src->mimeType();
        $quality = $src->options()['quality'] ?? null;
        
        // Process:
        try {
            $imager = $this->imager->resource($resource);
            
            // Option actions such as crop, greyscale, e.g.:
            if (
                isset($src->options()['actions'])
                && is_array($src->options()['actions'])
            ) {
                foreach($this->createActions($src->options()['actions']) as $action) {
                    $imager = $imager->action($action);
                }
            }
            
            // Fit and resize actions:
            if (!is_null($src->width()) && !is_null($src->height())) {
                $imager->action(new Action\Fit(width: $src->width(), height: $src->height(), upsize: $this->upsize));
            } else {
                $imager->action(new Action\Resize(width: $src->width(), height: $src->height(), upsize: $this->upsize));
            }
            
            $encoded = $imager->action(new Action\Encode(mimeType: $mimeType, quality: $quality));
            
            return $src->withEncoded($encoded);
        } catch (ImagerException $e) {
            throw new PictureCreateException(
                resource: $resource,
                message: $e->getMessage(),
                code: (int)$e->getCode(),
                previous: $e,
            );
        }
    }
    
    /**
     * Determine if to skip encoded src.
     *
     * @param SrcInterface $src
     * @param bool $log
     * @return bool
     */
    protected function skipEncodedSrc(SrcInterface $src, bool $log = true): bool
    {
        if (is_null($src->encoded())) {
            return true;
        }
        
        if (! $this->skipSmallerSizedSrc) {
            return false;
        }
        
        if (!is_null($src->width()) && $src->width() !== $src->encoded()->width()) {
            if ($log) {
                $this->logger?->log('debug', sprintf('Skipped src with width %s as lower sized', (string)$src->width()));
            }
            return true;
        }
        
        if (!is_null($src->height()) && $src->height() !== $src->encoded()->height()) {
            if ($log) {
                $this->logger?->log('debug', sprintf('Skipped src with height %s as lower sized', (string)$src->height()));
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * Returns the disallowed actions.
     *
     * @return array<array-key, class-string>
     */
    protected function disallowedActions(): array
    {
        // never allow these response actions:
        $disallowed = [
            Action\Save::class,
            Action\Encode::class,
        ];
        
        return array_merge($this->disallowedActions, $disallowed);
    }
    
    /**
     * Returns the created actions.
     *
     * @param array $actions
     * @return array<array-key, ActionInterface>
     */
    protected function createActions(array $actions): array
    {
        $created = [];
        
        foreach($actions as $actionName => $actionParams) {
            
            if ($actionParams instanceof ActionInterface) {
                $created[] = $actionParams;
                continue;
            }
            
            if (!is_string($actionName) || !is_array($actionParams)) {
                continue;
            }
            
            try {
                $action = $this->actionFactory->createAction($actionName, $actionParams);
                
                if (in_array($action::class, $this->disallowedActions())) {
                    $this->logger?->log('debug', sprintf('Disallowed action %s', $actionName));
                } else {
                    $created[] = $action;
                }
            } catch (ActionCreateException $e) {
                // ignore exception but we log:
                $this->logger?->log(
                    'debug',
                    sprintf('Unable to create action %s', $actionName),
                    ['exception' => $e]
                );
            }
        }
        
        return $created;
    }
    
    /**
     * Verify the mime type.
     *
     * @param ResourceInterface $resource
     * @return string The verified mime type.
     * @throws PictureCreateException
     * @psalm-suppress InvalidNullableReturnType
     * @psalm-suppress NullableReturnStatement
     */
    protected function verifyMimeType(ResourceInterface $resource): string
    {
        $detector = new FinfoMimeTypeDetector();
        
        switch ($resource) {
            case $resource instanceof Resource\Stream:
                $mimeType = $detector->detectMimeTypeFromBuffer((string)$resource->stream());
                break;
            case $resource instanceof Resource\File:
                $mimeType = $detector->detectMimeTypeFromFile($resource->file()->getFile());
                break;
            case $resource instanceof Resource\Binary:
                $mimeType = $detector->detectMimeTypeFromBuffer($resource->data());
                break;
            case $resource instanceof Resource\Base64:
                $mimeType = $detector->detectMimeTypeFromBuffer(base64_decode($resource->data()));
                break;
            default:
                throw new PictureCreateException(
                    resource: $resource,
                    message: 'Unsupported resource to determine mime type',
                );
        }
        
        if (!$this->isSupportedMimeType($mimeType)) {
            throw new PictureCreateException(
                resource: $resource,
                message: 'Unsupported mime type',
            );
        }
        
        return $mimeType;
    }
    
    /**
     * Determines if the the mime type is supported.
     *
     * @param mixed $mimeType
     * @return bool True if supported, otherwise false.
     */
    protected function isSupportedMimeType(mixed $mimeType): bool
    {
        if (!is_string($mimeType)) {
            return false;
        }
        
        $formats = new ImageFormats();
        $format = $formats->getFormat($mimeType);
        
        if (
            is_null($format)
            || !in_array($mimeType, $this->supportedMimeTypes)
        ) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Verify the mime type.
     *
     * @param ResourceInterface $resource
     * @param PictureInterface $picture
     * @return void
     * @throws PictureCreateException
     */
    protected function verifySizes(ResourceInterface $resource, PictureInterface $picture): void
    {
        switch ($resource) {
            case $resource instanceof Resource\Stream:
                $size = getimagesizefromstring((string)$resource->stream());
                $width = $size[0] ?? 0;
                $height = $size[1] ?? 0;
                break;
            case $resource instanceof Resource\File:
                $width = $resource->file()->getImageSize(0);
                $height = $resource->file()->getImageSize(1);
                break;
            case $resource instanceof Resource\Binary:
                $size = getimagesizefromstring($resource->data());
                $width = $size[0] ?? 0;
                $height = $size[1] ?? 0;
                break;
            case $resource instanceof Resource\Base64:
                $size = getimagesizefromstring(base64_decode($resource->data()));
                $width = $size[0] ?? 0;
                $height = $size[1] ?? 0;
                break;
            default:
                throw new PictureCreateException(
                    resource: $resource,
                    message: 'Unsupported resource to determine size',
                );
        }
        
        foreach($picture->srces() as $src) {
            if ($src->width() > $width) {
                throw new ResourceSizeException(
                    resource: $resource,
                    message: 'Image width to small to create images',
                );
            }
            
            if ($src->height() > $height) {
                throw new ResourceSizeException(
                    resource: $resource,
                    message: 'Image height to small to create images',
                );
            }
        }
    }
}