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

use Tobento\Service\Collection\Collection;

use Throwable;

/**
 * PictureFactory
 */
class PictureFactory implements PictureFactoryInterface
{
    /**
     * Create a new PictureFactory.
     *
     * @param $throwOnError = false
     */
    public function __construct(
        protected bool $throwOnError = false,
    ) {}
    
    /**
     * Create a new picture from array.
     *
     * @param array $picture
     * @return PictureInterface
     */
    public function createFromArray(array $picture): PictureInterface
    {
        $picture = new Collection($picture);
        
        return new Picture(
            img: $this->createImg($picture),
            sources: $this->createSources($picture),
            attributes: $picture->get('attributes', []),
            options: $picture->get('options', []),
        );
    }
    
    /**
     * Create img from definition.
     *
     * @param Collection $def
     * @return ImgInterface
     */
    protected function createImg(Collection $def): ImgInterface
    {
        try {
            $src = new Src(...$def->get('img.src', []));
        } catch (Throwable $e) {
            if ($this->throwOnError) {
                throw $e;
            }
            
            $src = new Src();
        }
        
        return new Img(
            src: $src,
            srcset: $this->createSrcset($def->get('img.srcset')),
            attributes: $def->get('img.attributes', []),
        );
    }
    
    /**
     * Create sources from definition.
     *
     * @param Collection $def
     * @return SourcesInterface
     */
    protected function createSources(Collection $def): SourcesInterface
    {
        if (! $def->has('sources')) {
            return new Sources();
        }
        
        $sources = [];
        
        foreach($def->get('sources', []) as $source) {
            
            if (!is_array($source)) {
                continue;
            }

            if (is_null($srcset = $this->createSrcset($source['srcset'] ?? null))) {
                continue;
            }
            
            $sources[] = new Source(srcset: $srcset, attributes: $source['attributes'] ?? []);
        }
        
        return new Sources(...$sources);
    }
    
    /**
     * Create srcset.
     *
     * @param mixed $srcset
     * @return null|SrcsetInterface
     */
    protected function createSrcset(mixed $srcset): null|SrcsetInterface
    {
        if (!is_array($srcset)) {
            return null;
        }
        
        $srces = [];

        foreach($srcset as $src) {
            try {
                $srces[] = new Src(...$src);
            } catch (Throwable $e) {
                if ($this->throwOnError) {
                    throw $e;
                }
            }
        }
        
        return new Srcset(...$srces);
    }
}