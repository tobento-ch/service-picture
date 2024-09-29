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
 * Sources
 */
class Sources implements SourcesInterface
{
    /**
     * @var array<array-key, SourceInterface>
     */    
    protected array $sources = [];
    
    /**
     * Create a new Sources.
     *
     * @param SourceInterface ...$source
     */
    public function __construct(
        SourceInterface ...$source,
    ) {
        $this->sources = $source;
    }
    
    /**
     * Returns all sources.
     *
     * @return array<array-key, SourceInterface>
     */
    public function all(): array
    {
        return $this->sources;
    }
    
    /**
     * Returns an iterator for the sources.
     *
     * @return Generator
     */
    public function getIterator(): Generator
    {
        foreach($this->sources as $source) {
            yield $source;
        }
    }
    
    /**
     * Returns the number of sources.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->sources);
    }
    
    /**
     * Returns all srces.
     *
     * @return Generator
     */
    public function srces(): Generator
    {
        foreach($this->sources as $source) {
            yield from $source->srcset();
        }
    }
    
    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $sources = [];
        
        foreach($this->all() as $source) {
            $sources[] = $source->jsonSerialize();
        }
        
        return $sources;
    }
}