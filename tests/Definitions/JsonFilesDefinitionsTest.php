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

namespace Tobento\Service\Picture\Test\Definitions;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Dir\Dir;
use Tobento\Service\Dir\Dirs;
use Tobento\Service\Picture\Definition\ArrayDefinition;
use Tobento\Service\Picture\DefinitionInterface;
use Tobento\Service\Picture\Definitions\Definitions;
use Tobento\Service\Picture\Definitions\JsonFilesDefinitions;
use Tobento\Service\Picture\DefinitionsInterface;
use Tobento\Service\Picture\Exception\DefinitionNotFoundException;

class JsonFilesDefinitionsTest extends TestCase
{
    public function testThatImplementsDefinitionsInterface()
    {
        $this->assertInstanceof(
            DefinitionsInterface::class,
            new JsonFilesDefinitions(name: 'foo', dirs: new Dirs())
        );
    }
    
    public function testNameMethod()
    {
        $this->assertSame('foo', (new JsonFilesDefinitions(name: 'foo', dirs: new Dirs()))->name());
    }
    
    public function testAddMethod()
    {
        $definitions = new JsonFilesDefinitions(name: 'foo', dirs: new Dirs());
        $definition = new ArrayDefinition('bar', ['img' => ['src' => [50]]]);
        $definitions->add($definition);
        
        $this->assertTrue($definitions->has('bar'));
        $this->assertSame($definition, $definitions->get('bar'));
    }
    
    public function testAddMethodWithDefinitions()
    {
        $definitions = new JsonFilesDefinitions(name: 'foo', dirs: new Dirs());
        $definition = new ArrayDefinition('bar', ['img' => ['src' => [50]]]);
        $definitions->add(new Definitions('baz', $definition));
        
        $this->assertTrue($definitions->has('bar'));
        $this->assertSame($definition, $definitions->get('bar'));
    }
    
    public function testHasMethod()
    {
        $definitions = new JsonFilesDefinitions(
            name: 'foo',
            dirs: new Dirs(new Dir(realpath(__DIR__.'/..').'/src/definitions/'))
        );
        
        $this->assertTrue($definitions->has('blog'));
        $this->assertTrue($definitions->has('product'));
        $this->assertFalse($definitions->has('bar'));
        
        $definitions->add(new ArrayDefinition('bar', ['img' => ['src' => [50]]]));
        
        $this->assertTrue($definitions->has('bar'));
    }
    
    public function testGetMethod()
    {
        $definitions = new JsonFilesDefinitions(
            name: 'foo',
            dirs: new Dirs(new Dir(realpath(__DIR__.'/..').'/src/definitions/'))
        );
        
        $definition = new ArrayDefinition('bar', ['img' => ['src' => [50]]]);
        $definitions->add($definition);
        
        $this->assertNotNull($definitions->get('blog'));
        $this->assertNotNull($definitions->get('product'));
        $this->assertSame($definition, $definitions->get('bar'));
    }
    
    public function testGetMethodThrowsDefinitionNotFoundExceptionIfNotFound()
    {
        $this->expectException(DefinitionNotFoundException::class);
        
        $definitions = new JsonFilesDefinitions(name: 'foo', dirs: new Dirs());
        $definitions->get('bar');
    }
    
    public function testFilterMethod()
    {
        $definitions = new JsonFilesDefinitions(
            name: 'foo',
            dirs: new Dirs(new Dir(realpath(__DIR__.'/..').'/src/definitions/'))
        );
        
        $definitions->add(new ArrayDefinition('baz', ['img' => ['src' => [50]]]));
        
        $this->assertSame(4, iterator_count($definitions));
        
        $definitionsNew = $definitions->filter(
            fn(DefinitionInterface $d): bool => $d->toPicture()->img()->src()->width() === 50
        );
        
        $this->assertFalse($definitions === $definitionsNew);
        $this->assertSame(1, iterator_count($definitionsNew));
    }
    
    public function testAllMethod()
    {
        $definitions = new JsonFilesDefinitions(
            name: 'foo',
            dirs: new Dirs(new Dir(realpath(__DIR__.'/..').'/src/definitions/'))
        );
        
        $definitions->add(new ArrayDefinition('baz', ['img' => ['src' => [50]]]));
        
        $this->assertSame(4, iterator_count($definitions->all()));
    }
    
    public function testGetIteratorMethod()
    {
        $definitions = new JsonFilesDefinitions(
            name: 'foo',
            dirs: new Dirs(new Dir(realpath(__DIR__.'/..').'/src/definitions/'))
        );
        
        $definitions->add(new ArrayDefinition('baz', ['img' => ['src' => [50]]]));
        
        $iterated = [];
        
        foreach($definitions as $definition) {
            $this->assertInstanceof(DefinitionInterface::class, $definition);
            $iterated[] = $definition;
        }
        
        $this->assertSame(4, count($iterated));
    }
}