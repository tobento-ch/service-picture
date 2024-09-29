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

/**
 * Img
 */
class Img implements ImgInterface
{
    /**
     * Create a new Img.
     *
     * @param SrcInterface $src
     * @param null|SrcsetInterface $srcset
     * @param array $attributes
     */
    public function __construct(
        protected SrcInterface $src,
        protected null|SrcsetInterface $srcset = null,
        protected array $attributes = [],
    ) {}

    /**
     * Returns the src.
     *
     * @return SrcInterface
     */
    public function src(): SrcInterface
    {
        return $this->src;
    }
    
    /**
     * Returns a new instance with the given src.
     *
     * @param SrcInterface $src
     * @return static
     */
    public function withSrc(SrcInterface $src): static
    {
        $new = clone $this;
        $new->src = $src;
        return $new;
    }
    
    /**
     * Returns the srcset.
     *
     * @return SrcsetInterface
     */
    public function srcset(): null|SrcsetInterface
    {
        return $this->srcset;
    }
    
    /**
     * Returns a new instance with the given srcset.
     *
     * @param null|SrcsetInterface $srcset
     * @return static
     */
    public function withSrcset(null|SrcsetInterface $srcset): static
    {
        $new = clone $this;
        $new->srcset = $srcset;
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
     * Serializes the object to a value that can be serialized natively by json_encode().
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'src' => $this->src()->jsonSerialize(),
            'srcset' => $this->srcset()?->jsonSerialize(),
            'attributes' => $this->attributes(),
        ];
    }
}