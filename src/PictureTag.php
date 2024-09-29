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

use Tobento\Service\Tag\TagInterface;
use Tobento\Service\Tag\Tag;

/**
 * PictureTag
 */
class PictureTag implements PictureTagInterface
{
    /**
     * @var array<array-key, TagInterface>
     */
    protected array $sources = [];
    
    /**
     * Create a new PictureTag.
     *
     * @param TagInterface $tag
     * @param TagInterface $img
     * @param TagInterface ...$source
     */
    public function __construct(
        protected TagInterface $tag,
        protected TagInterface $img,
        TagInterface ...$source,
    ) {
        if ($tag->getName() !== 'picture') {
            $this->tag = $tag->withName('picture');
        }
        
        if ($img->getName() !== 'img') {
            $this->img = $img->withName('img');
        }
        
        $this->sources = $source;
    }

    /**
     * Returns the picture tag.
     *
     * @return TagInterface
     */
    public function tag(): TagInterface
    {
        return $this->tag;
    }
    
    /**
     * Returns a new instance with the given picture tag.
     *
     * @param TagInterface $tag
     * @return static
     */
    public function withTag(TagInterface $tag): static
    {
        if ($tag->getName() !== 'picture') {
            $tag = $tag->withName('picture');
        }
        
        $new = clone $this;
        $new->tag = $tag;
        return $new;
    }
    
    /**
     * Returns the img.
     *
     * @return TagInterface
     */
    public function img(): TagInterface
    {
        return $this->img;
    }
    
    /**
     * Returns a new instance with the given img tag.
     *
     * @param TagInterface $img
     * @return static
     */
    public function withImg(TagInterface $img): static
    {
        if ($img->getName() !== 'img') {
            $img = $img->withName('img');
        }
        
        $new = clone $this;
        $new->img = $img;
        return $new;
    }
    
    /**
     * Returns the sources.
     *
     * @return array<array-key, TagInterface>
     */
    public function sources(): array
    {
        return $this->sources;
    }
    
    /**
     * Returns a new instance with the given source tags.
     *
     * @param TagInterface ...$source
     * @return static
     */
    public function withSources(TagInterface ...$source): static
    {
        $new = clone $this;
        $new->sources = $source;
        return $new;
    }
    
    /**
     * Returns a new instance with the specified attribute.
     * Class must add to existing classes if a string is provided,
     * otherwise overwrite.     
     *
     * @param string $name
     * @param mixed $value
     * @return static
     */
    public function attr(string $name, mixed $value = null): static
    {
        if ($name === 'class' && is_string($value)) {
            return $this->withTag($this->tag()->class($value));
        }
        
        return $this->withTag($this->tag()->attr($name, $value));
    }
    
    /**
     * Returns a new instance with the specified attribute.
     * Class must add to existing classes if a string is provided,
     * otherwise overwrite.     
     *
     * @param string $name
     * @param mixed $value
     * @return static
     */
    public function imgAttr(string $name, mixed $value = null): static
    {
        if ($name === 'class' && is_string($value)) {
            return $this->withImg($this->img()->class($value));
        }
        
        return $this->withImg($this->img()->attr($name, $value));
    }
    
    /**
     * Returns the evaluated html of the icon. Must be escaped.
     *
     * @return string
     */
    public function render(): string
    {
        if (empty($this->img()->attributes()->get('src'))) {
            return '';
        }
        
        $html = '';
        
        foreach($this->sources() as $source) {
            if ($source->getName() === 'source') {
                $html .= (string)$source;
            }
        }
        
        $html .= (string)$this->img();
        
        return (string)$this->tag()->withHtml($html);
    }
    
    /**
     * Returns the string representation of the icon. Must be escaped.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }
}