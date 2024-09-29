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
 * StackDefinitions
 */
class StackDefinitions implements DefinitionsInterface
{
    /**
     * @var array<array-key, DefinitionsInterface>
     */
    protected array $definitions = [];
    
    /**
     * @var Definitions
     */
    protected Definitions $definitionsCollection;
    
    /**
     * Create a new StackDefinitions instance.
     *
     * @param string $name
     * @param DefinitionsInterface ...$definitions
     */
    final public function __construct(
        protected string $name,
        DefinitionsInterface ...$definitions
    ) {
        $this->definitions = $definitions;
        $this->definitionsCollection = new Definitions($name);
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
        if ($definition instanceof DefinitionsInterface) {
            $this->definitions[] = $definition;
        } else {
            $this->definitionsCollection->add($definition);
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
        foreach($this->allDefinitions() as $definitions) {
            if ($definitions->has($definition)) {
                return true;
            }
        }
        
        return false;
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
        foreach($this->allDefinitions() as $definitions) {
            if ($definitions->has($definition)) {
                return $definitions->get($definition);
            }
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
        $filtered = [];
        
        foreach($this->allDefinitions() as $definitions) {
            $filtered[] = $definitions->filter($callback);
        }
        
        return new static($this->name(), ...$filtered);
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
        foreach($this->definitions as $definitions) {
            yield from $definitions;
        }
        
        yield from $this->definitionsCollection;
    }
    
    /**
     * Returns all definitions.
     *
     * @return \Generator<DefinitionsInterface>
     */
    protected function allDefinitions(): \Generator
    {
        foreach($this->definitions as $definitions) {
            yield $definitions;
        }
        
        yield $this->definitionsCollection;
    }
}