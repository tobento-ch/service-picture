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
use Tobento\Service\Picture\Definition\ArrayDefinition;
use Tobento\Service\Picture\DefinitionInterface;
use Tobento\Service\Picture\Definitions\Definitions;
use Tobento\Service\Picture\DefinitionsInterface;
use Tobento\Service\Picture\Exception\DefinitionNotFoundException;

class DefinitionsTest extends TestCase
{
    public function testThatImplementsDefinitionsInterface()
    {
        $this->assertInstanceof(
            DefinitionsInterface::class,
            new Definitions(name: 'foo')
        );
    }
    
    public function testNameMethod()
    {
        $this->assertSame('foo', (new Definitions(name: 'foo'))->name());
    }
    
    public function testAddMethod()
    {
        $definitions = new Definitions(name: 'foo');
        $definition = new ArrayDefinition('bar', ['img' => ['src' => [50]]]);
        $definitions->add($definition);
        
        $this->assertTrue($definitions->has('bar'));
        $this->assertSame($definition, $definitions->get('bar'));
    }
    
    public function testAddMethodWithDefinitions()
    {
        $definitions = new Definitions(name: 'foo');
        $definition = new ArrayDefinition('bar', ['img' => ['src' => [50]]]);
        $definitions->add(new Definitions('baz', $definition));
        
        $this->assertTrue($definitions->has('bar'));
        $this->assertSame($definition, $definitions->get('bar'));
    }
    
    public function testHasMethod()
    {
        $definitions = new Definitions(name: 'foo');
        
        $this->assertFalse($definitions->has('bar'));
        
        $definitions->add(new ArrayDefinition('bar', ['img' => ['src' => [50]]]));
        
        $this->assertTrue($definitions->has('bar'));
    }
    
    public function testGetMethod()
    {
        $definitions = new Definitions(name: 'foo');
        $definition = new ArrayDefinition('bar', ['img' => ['src' => [50]]]);
        $definitions->add($definition);
        
        $this->assertSame($definition, $definitions->get('bar'));
    }
    
    public function testGetMethodThrowsDefinitionNotFoundExceptionIfNotFound()
    {
        $this->expectException(DefinitionNotFoundException::class);
        
        $definitions = new Definitions(name: 'foo');
        $definitions->get('bar');
    }
    
    public function testFilterMethod()
    {
        $definitions = new Definitions(
            'foo',
            new ArrayDefinition('foo', ['img' => ['src' => [50]]]),
            new ArrayDefinition('bar', ['img' => ['src' => [80]]]),
        );
        
        $definitions->add(new ArrayDefinition('baz', ['img' => ['src' => [50]]]));
        
        $this->assertSame(3, iterator_count($definitions));
        
        $definitionsNew = $definitions->filter(
            fn(DefinitionInterface $d): bool => $d->toPicture()->img()->src()->width() === 50
        );
        
        $this->assertFalse($definitions === $definitionsNew);
        $this->assertSame(2, iterator_count($definitionsNew));
    }
    
    public function testAllMethod()
    {
        $definitions = new Definitions(
            'foo',
            new ArrayDefinition('foo', ['img' => ['src' => [50]]]),
            new ArrayDefinition('bar', ['img' => ['src' => [80]]]),
        );
        
        $definitions->add(new ArrayDefinition('baz', ['img' => ['src' => [50]]]));
        
        $this->assertSame(3, iterator_count($definitions->all()));
    }
    
    public function testGetIteratorMethod()
    {
        $definitions = new Definitions(
            'foo',
            new ArrayDefinition('foo', ['img' => ['src' => [50]]]),
            new ArrayDefinition('bar', ['img' => ['src' => [80]]]),
        );
        
        $definitions->add(new ArrayDefinition('baz', ['img' => ['src' => [50]]]));
        
        $iterated = [];
        
        foreach($definitions as $definition) {
            $this->assertInstanceof(DefinitionInterface::class, $definition);
            $iterated[] = $definition;
        }
        
        $this->assertSame(3, count($iterated));
    }
}