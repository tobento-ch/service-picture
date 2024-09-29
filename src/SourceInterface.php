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
 * SourceInterface
 */
interface SourceInterface extends JsonSerializable
{
    /**
     * Returns the srcset.
     *
     * @return SrcsetInterface
     */
    public function srcset(): SrcsetInterface;
    
    /**
     * Returns a new instance with the given srcset.
     *
     * @param SrcsetInterface $srcset
     * @return static
     */
    public function withSrcset(SrcsetInterface $srcset): static;
    
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