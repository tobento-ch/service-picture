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

use IteratorAggregate;
use Tobento\Service\Picture\Exception\DefinitionNotFoundException;

/**
 * DefinitionsInterface
 */
interface DefinitionsInterface extends IteratorAggregate
{
    /**
     * Returns a definitions name.
     *
     * @return string
     */
    public function name(): string;
    
    /**
     * Adds a definition or definitions.
     *
     * @param DefinitionInterface|DefinitionsInterface $definition
     * @return static $this
     */
    public function add(DefinitionInterface|DefinitionsInterface $definition): static;
    
    /**
     * Returns true if the specified definition exists, otherwise false.
     *
     * @param string $definition The definition name.
     * @return bool
     */
    public function has(string $definition): bool;
    
    /**
     * Returns the definition.
     *
     * @param string $definition The definition name.
     * @return DefinitionInterface
     * @throws DefinitionNotFoundException
     */
    public function get(string $definition): DefinitionInterface;
    
    /**
     * Returns a new instance with the definitions filtered.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static;
    
    /**
     * Returns all definitions.
     *
     * @return iterable<DefinitionInterface>
     */
    public function all(): iterable;
}