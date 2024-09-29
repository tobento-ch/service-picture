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
 * Srcset
 */
class Srcset implements SrcsetInterface
{
    /**
     * @var array<array-key, SrcInterface>
     */
    protected array $srces = [];
    
    /**
     * Create a new Srcset.
     *
     * @param SrcInterface $src
     */
    public function __construct(
        SrcInterface ...$src,
    ) {
        $this->srces = $src;
    }

    /**
     * Returns all srces.
     *
     * @return array<array-key, SrcInterface>
     */
    public function all(): array
    {
        return $this->srces;
    }
    
    /**
     * Returns an iterator for the srces.
     *
     * @return Generator
     */
    public function getIterator(): Generator
    {
        foreach($this->srces as $src) {
            yield $src;
        }
    }
    
    /**
     * Returns the number of srces.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->srces);
    }
    
    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $srces = [];
        
        foreach($this->all() as $src) {
            $srces[] = $src->jsonSerialize();
        }
        
        return $srces;
    }
}