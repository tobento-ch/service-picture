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

use Generator;

/**
 * Picture
 */
class Picture implements PictureInterface
{
    /**
     * Create a new Picture.
     *
     * @param ImgInterface $img
     * @param SourcesInterface $sources
     * @param array $attributes
     * @param array $options
     */
    public function __construct(
        protected ImgInterface $img,
        protected SourcesInterface $sources,
        protected array $attributes = [],
        protected array $options = [],
    ) {}

    /**
     * Returns the img.
     *
     * @return ImgInterface
     */
    public function img(): ImgInterface
    {
        return $this->img;
    }
    
    /**
     * Returns a new instance with the given img.
     *
     * @param ImgInterface $img
     * @return static
     */
    public function withImg(ImgInterface $img): static
    {
        $new = clone $this;
        $new->img = $img;
        return $new;
    }
    
    /**
     * Returns the sources.
     *
     * @return SourcesInterface
     */
    public function sources(): SourcesInterface
    {
        return $this->sources;
    }
    
    /**
     * Returns a new instance with the given sources.
     *
     * @param SourcesInterface $sources
     * @return static
     */
    public function withSources(SourcesInterface $sources): static
    {
        $new = clone $this;
        $new->sources = $sources;
        return $new;
    }
    
    /**
     * Returns the attributes.
     *
     * @return array
     */
    public function attributes(): array
    {
        return $this->attributes;
    }
    
    /**
     * Returns a new instance with the given attributes.
     *
     * @param array $attributes
     * @return static
     */
    public function withAttributes(array $attributes): static
    {
        $new = clone $this;
        $new->attributes = $attributes;
        return $new;
    }
    
    /**
     * Returns the options.
     *
     * @return array
     */
    public function options(): array
    {
        return $this->options;
    }
    
    /**
     * Returns a new instance with the given options.
     *
     * @param array $options
     * @return static
     */
    public function withOptions(array $options): static
    {
        $new = clone $this;
        $new->options = $options;
        return $new;
    }
    
    /**
     * Returns the srces.
     *
     * @return Generator
     */
    public function srces(): Generator
    {
        yield $this->img()->src();
        
        if ($this->img()->srcset()) {
            yield from $this->img()->srcset();
        }
        
        yield from $this->sources()->srces();
    }
    
    /**
     * Returns a new created picture tag.
     *
     * @return PictureTagInterface
     */
    public function toTag(): PictureTagInterface
    {
        return (new PictureTagFactory())->createFromPicture($this);
    }
    
    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'img' => $this->img()->jsonSerialize(),
            'sources' => $this->sources()->jsonSerialize(),
            'attributes' => $this->attributes(),
            'options' => $this->options(),
        ];
    }
    
    /**
     * Returns the string representation of the picture.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->toTag();
    }
}