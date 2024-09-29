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

use Tobento\Service\Dir\DirsInterface;
use Tobento\Service\Filesystem\Dir;
use Tobento\Service\Filesystem\JsonFile;
use Tobento\Service\Picture\Definition\PictureDefinition;
use Tobento\Service\Picture\DefinitionInterface;
use Tobento\Service\Picture\DefinitionsInterface;
use Tobento\Service\Picture\Exception\DefinitionNotFoundException;
use Tobento\Service\Picture\PictureFactory;

/**
 * JsonFilesDefinitions: loads definitions from the given directories.
 */
class JsonFilesDefinitions implements DefinitionsInterface
{
    /**
     * @var array<string, DefinitionInterface>
     */
    protected array $definitions = [];
    
    /**
     * @var array<string, bool>
     */
    protected array $notFoundDefinitions = [];
    
    /**
     * @var bool $collected
     */
    protected bool $collected = false;
    
    /**
     * Create a new Definitions instance.
     *
     * @param string $name
     * @param DirsInterface $dirs
     */
    public function __construct(
        protected string $name,
        protected DirsInterface $dirs,
    ) {}

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
        if (isset($this->definitions[$definition])) {
            return true;
        }

        if ($foundDefinition = $this->findDefinition($definition)) {
            $this->definitions[$definition] = $foundDefinition;
            return true;
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
        if (isset($this->definitions[$definition])) {
            return $this->definitions[$definition];
        }

        if ($foundDefinition = $this->findDefinition($definition)) {
            return $this->definitions[$definition] = $foundDefinition;
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
        $this->collectDefinitions();
        
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
        $this->collectDefinitions();
        
        foreach($this->definitions as $definition) {
            yield $definition;
        }
    }
    
    /**
     * Returns the found definition or null if not found.
     *
     * @param string $definition The definition name.
     * @return null|DefinitionInterface
     */
    protected function findDefinition(string $definition): null|DefinitionInterface
    {
        if (isset($this->notFoundDefinitions[$definition])) {
            return null;
        }
        
        $filename = preg_replace('/[^A-Za-z0-9_\-\']/', '-', trim($definition));
        
        foreach($this->dirs->all() as $directory) {
            $jsonFile = new JsonFile($directory->dir().basename($filename).'.json');
            
            if (!$jsonFile->isFile()) {
                continue;
            }

            try {
                return new PictureDefinition(
                    name: $definition,
                    picture: (new PictureFactory())->createFromArray($jsonFile->toArray()),
                );
            } catch (\Throwable $e) {
                continue;
            }
        }
        
        $this->notFoundDefinitions[$definition] = true;
        return null;
    }
    
    /**
     * Collects all file defintions.
     *
     * @return void
     */
    protected function collectDefinitions(): void
    {
        if ($this->collected) {
            return;
        }
        
        $dir = new Dir();
        $pictureFactory = new PictureFactory();
        
        foreach($this->dirs->all() as $directory) {
            $files = $dir->getFiles($directory->dir());
            
            foreach($files as $file) {
                $jsonFile = new JsonFile($file->getFile());
                $definition = $jsonFile->getFilename();
                
                if (isset($this->definitions[$definition])) {
                    continue;
                }
                
                try {
                    $this->definitions[$definition] = new PictureDefinition(
                        name: $definition,
                        picture: $pictureFactory->createFromArray($jsonFile->toArray()),
                    );
                } catch (\Throwable $e) {
                    continue;
                }
            }
        }
        
        $this->collected = true;
    }
}