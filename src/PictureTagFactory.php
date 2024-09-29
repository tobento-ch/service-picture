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

use Tobento\Service\Tag\Attributes;
use Tobento\Service\Tag\Tag;

/**
 * PictureTagFactory
 */
class PictureTagFactory implements PictureTagFactoryInterface
{
    /**
     * Create a new picture tag from picture.
     *
     * @param PictureInterface $picture
     * @return PictureTagInterface
     */
    public function createFromPicture(PictureInterface $picture): PictureTagInterface
    {
        $sources = [];
        
        foreach($picture->sources() as $source) {
            
            if ($source->srcset()->count() < 1) {
                continue;
            }
            
            $srcset = [];
            $mimeTypes = [];
            $sourceAttributes = $source->attributes();
            
            foreach($source->srcset() as $src) {
                
                if ($src->mimeType()) {
                    $mimeTypes[] = $src->mimeType();
                }
                
                $srcset[] = $this->extractPath(src: $src, withDescriptor: true);
            }
            
            $mimeTypes = array_unique($mimeTypes);
            
            if (isset($sourceAttributes['type'])) {
                if (count($mimeTypes) > 1) {
                    unset($sourceAttributes['type']);
                } elseif (count($mimeTypes) === 1) {
                    if ($mimeTypes[0] !== $sourceAttributes['type']) {
                        $sourceAttributes['type'] = $mimeTypes[0];
                    }
                }
            }
            
            $attributes = new Attributes($sourceAttributes);
            $attributes->set('srcset', implode(', ', $srcset));
            
            $sources[] = new Tag(name: 'source', attributes: $attributes);
        }

        // picture tag:
        $tag = new Tag(
            name: 'picture',
            attributes: new Attributes($picture->attributes()),
            renderEmptyTag: false,
        );
        
        // img tag:
        $imgSrc = $this->extractPath($picture->img()->src());
        
        if ($imgSrc === '') {
            return new NullPictureTag();
        }
        
        $attributes = new Attributes($picture->img()->attributes());
        $attributes->set('src', $imgSrc);
        
        if (
            ! $attributes->has('width')
            && !is_null($width = $this->extractWidth(src: $picture->img()->src()))
        ) {
            $attributes->set('width', (string)$width);
        }
        
        if (
            ! $attributes->has('height')
            && !is_null($height = $this->extractHeight(src: $picture->img()->src()))
        ) {
            $attributes->set('height', (string)$height);
        }
        
        // handle img srcset:
        if ($picture->img()->srcset()?->count() >= 1) {
            $srcset = [];
            
            foreach($picture->img()->srcset() as $src) {
                $srcset[] = $this->extractPath(src: $src, withDescriptor: true);
            }
            
            $attributes->set('srcset', implode(', ', $srcset));
        }
        
        $img = new Tag(name: 'img', attributes: $attributes);
        
        return new PictureTag($tag, $img, ...$sources);
    }
    
    /**
     * Extract the path from the src.
     *
     * @param SrcInterface $src
     * @param bool $withDescriptor
     * @return string
     */
    protected function extractPath(SrcInterface $src, bool $withDescriptor = false): string
    {
        $path = '';
        
        if ($src->url()) {
            $path = $src->url();
        } elseif ($src->encoded()) {
            $path = $src->encoded()->dataUrl();
        } elseif ($src->path()) {
            $path = $src->path();
        }
        
        if ($withDescriptor) {
            $desciptor = empty($src->descriptor()) ? '' : ' '.$src->descriptor();
            return $path.$desciptor;
        }
        
        return is_null($path) ? '' : $path;
    }
    
    /**
     * Extract the width from the given src.
     *
     * @param SrcInterface $src
     * @return null|int
     */
    protected function extractWidth(SrcInterface $src): null|int
    {
        if ($src->encoded()) {
            return $src->encoded()->width();
        }
        
        return $src->width();
    }
    
    /**
     * Extract the height from the given src.
     *
     * @param SrcInterface $src
     * @return null|int
     */
    protected function extractHeight(SrcInterface $src): null|int
    {
        if ($src->encoded()) {
            return $src->encoded()->height();
        }
        
        return $src->height();
    }
}