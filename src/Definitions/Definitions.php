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

namespace Tobento\Service\Picture\Definitions;

use Tobento\Service\Picture\DefinitionInterface;
use Tobento\Service\Picture\DefinitionsInterface;
use Tobento\Service\Picture\Exception\DefinitionNotFoundException;

/**
 * Definitions
 */
class Definitions implements DefinitionsInterface
{
    /**
     * @var array<string, DefinitionInterface>
     */
    protected array $definitions = [];
    
    /**
     * Create a new Definitions instance.
     *
     * @param string $name
     * @param DefinitionInterface ...$definitions
     */
    public function __construct(
        protected string $name,
        DefinitionInterface ...$definitions
    ) {
        foreach($definitions as $definition) {
            $this->add($definition);
        }
    }

    /**
     * Returns a definitions name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
    
    /**
     * Adds a definition or definitions.
     *
     * @param DefinitionInterface|DefinitionsInterface $definition
     * @return static $this
     */
    public function add(DefinitionInterface|DefinitionsInterface $definition): static
    {
        if ($definition instanceof DefinitionInterface) {
            $this->definitions[$definition->name()] = $definition;
        } else {
            foreach($definition as $def) {
                $this->definitions[$def->name()] = $def;
            }
        }
        
        return $this;
    }
    
    /**
     * Returns true if the specified definition exists, otherwise false.
     *
     * @param string $definition The definition name.
     * @return bool
     */
    public function has(string $definition): bool
    {
        return isset($this->definitions[$definition]);
    }
    
    /**
     * Returns the definition.
     *
     * @param string $definition The definition name.
     * @return DefinitionInterface
     * @throws DefinitionNotFoundException
     */
    public function get(string $definition): DefinitionInterface
    {
        if (isset($this->definitions[$definition])) {
            return $this->definitions[$definition];
        }
        
        throw new DefinitionNotFoundException($definition);
    }
    
    /**
     * Returns a new instance with the definitions filtered.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static
    {
        $new = clone $this;
        $new->definitions = array_filter($this->definitions, $callback);
        return $new;
    }
    
    /**
     * Returns all definitions.
     *
     * @return iterable<DefinitionInterface>
     */
    public function all(): iterable
    {
        return $this->getIterator();
    }
    
    /**
     * Returns an iterator for the definitions.
     *
     * @return \Generator<DefinitionInterface>
     */
    public function getIterator(): \Generator
    {
        foreach($this->definitions as $definition) {
            yield $definition;
        }
    }
}