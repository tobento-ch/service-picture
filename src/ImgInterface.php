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

use JsonSerializable;

/**
 * ImgInterface
 */
interface ImgInterface extends JsonSerializable
{
    /**
     * Returns the src.
     *
     * @return SrcInterface
     */
    public function src(): SrcInterface;
    
    /**
     * Returns a new instance with the given src.
     *
     * @param SrcInterface $src
     * @return static
     */
    public function withSrc(SrcInterface $src): static;
    
    /**
     * Returns the srcset.
     *
     * @return SrcsetInterface
     */
    public function srcset(): null|SrcsetInterface;
    
    /**
     * Returns a new instance with the given srcset.
     *
     * @param null|SrcsetInterface $srcset
     * @return static
     */
    public function withSrcset(null|SrcsetInterface $srcset): static;
    
    /**
     * Returns the attributes.
     *
     * @return array
     */
    public function attributes(): array;
    
    /**
     * Returns a new instance with the given attributes.
     *
     * @param array $attributes
     * @return static
     */
    public function withAttributes(array $attributes): static;
}