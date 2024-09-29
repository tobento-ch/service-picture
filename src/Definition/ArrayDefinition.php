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

namespace Tobento\Service\Picture\Definition;

use Tobento\Service\Collection\Collection;
use Tobento\Service\Picture\Img;
use Tobento\Service\Picture\ImgInterface;
use Tobento\Service\Picture\DefinitionInterface;
use Tobento\Service\Picture\Picture;
use Tobento\Service\Picture\PictureInterface;
use Tobento\Service\Picture\Source;
use Tobento\Service\Picture\Sources;
use Tobento\Service\Picture\SourcesInterface;
use Tobento\Service\Picture\Src;
use Tobento\Service\Picture\SrcInterface;
use Tobento\Service\Picture\Srcset;
use Tobento\Service\Picture\SrcsetInterface;

/**
 * ArrayDefinition
 */
class ArrayDefinition implements DefinitionInterface
{
    /**
     * Create a new ArrayDefinition.
     *
     * @param string $name
     * @param array $definition
     */
    public function __construct(
        protected string $name,
        protected array $definition,
    ) {}

    /**
     * Returns a definition name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    /**
     * To picture.
     *
     * @return PictureInterface
     */
    public function toPicture(): PictureInterface
    {
        $def = new Collection($this->definition);
        
        return new Picture(
            img: $this->createImg($def),
            sources: $this->createSources($def),
            attributes: $def->get('attributes', []),
            options: $def->get('options', []),
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
        if (! $def->has('img.src')) {
            throw new \InvalidArgumentException('Undefined img src!');
        }
        
        if ($def->get('img.src') instanceof SrcInterface) {
            $src = $def->get('img.src');
        } else {
            $src = new Src(
                width: $this->ensureIntOrNull($def->get('img.src.0')),
                height: $this->ensureIntOrNull($def->get('img.src.1')),
            );
        }
        
        $attributes = $def->get('img', []);
        unset($attributes['src']);
        unset($attributes['srcset']);
        
        return new Img(
            src: $src,
            srcset: $this->createSrcset($def->get('img.srcset')),
            attributes: $attributes,
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
            
            unset($source['srcset']);
            
            $sources[] = new Source(srcset: $srcset, attributes: $source);
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

        foreach($srcset as $descriptor => $dimension) {
            
            if ($dimension instanceof SrcInterface) {
                $srces[] = $dimension;
                continue;
            }
            
            if (!is_array($dimension) || !is_string($descriptor)) {
                continue;
            }

            $srces[] = new Src(
                width: $this->ensureIntOrNull($dimension[0] ?? null),
                height: $this->ensureIntOrNull($dimension[1] ?? null),
                descriptor: $descriptor,
            );
        }
        
        return new Srcset(...$srces);
    }
    
    /**
     * Ensure that the given value is either an int or null.
     *
     * @param mixed $value
     * @return null|int
     */
    protected function ensureIntOrNull(mixed $value): null|int
    {
        if (is_int($value) || is_null($value)) {
            return $value;
        } 
        
        return null;
    }
}