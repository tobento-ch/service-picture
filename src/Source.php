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
 * Source
 */
class Source implements SourceInterface
{
    /**
     * Create a new Source.
     *
     * @param SrcsetInterface $srcset
     * @param array $attributes
     */
    public function __construct(
        protected SrcsetInterface $srcset,
        protected array $attributes = [],
    ) {}

    /**
     * Returns the srcset.
     *
     * @return SrcsetInterface
     */
    public function srcset(): SrcsetInterface
    {
        return $this->srcset;
    }
    
    /**
     * Returns a new instance with the given srcset.
     *
     * @param SrcsetInterface $srcset
     * @return static
     */
    public function withSrcset(SrcsetInterface $srcset): static
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
            'srcset' => $this->srcset()->jsonSerialize(),
            'attributes' => $this->attributes(),
        ];
    }
}