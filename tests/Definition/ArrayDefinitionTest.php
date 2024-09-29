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

namespace Tobento\Service\Picture\Test\Definition;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Picture\Definition\ArrayDefinition;
use Tobento\Service\Picture\DefinitionInterface;
use Tobento\Service\Picture\Src;

class ArrayDefinitionTest extends TestCase
{
    public function testThatImplementsDefinitionInterface()
    {
        $this->assertInstanceof(
            DefinitionInterface::class,
            new ArrayDefinition(name: 'foo', definition: [])
        );
    }
    
    public function testNameMethod()
    {
        $this->assertSame('foo', (new ArrayDefinition(name: 'foo', definition: []))->name());
    }
    
    public function testToPictureMethodImgThrowsIfUndefinedImgSrc()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Undefined img src!');
        
        (new ArrayDefinition('foo', ['img' => []]))->toPicture();
    }
    
    public function testToPictureMethodImgSrcObject()
    {
        $src = new Src(width: 200);
        $this->assertSame($src, (new ArrayDefinition('foo', ['img' => ['src' => $src]]))->toPicture()->img()->src());
    }
    
    public function testToPictureMethodImgSrcWidth()
    {
        $this->assertSame(600, (new ArrayDefinition('foo', ['img' => ['src' => [600]]]))->toPicture()->img()->src()->width());
        $this->assertSame(null, (new ArrayDefinition('foo', ['img' => ['src' => [null]]]))->toPicture()->img()->src()->width());
        $this->assertSame(null, (new ArrayDefinition('foo', ['img' => ['src' => []]]))->toPicture()->img()->src()->width());
        $this->assertSame(null, (new ArrayDefinition('foo', ['img' => ['src' => 'foo']]))->toPicture()->img()->src()->width());
        $this->assertSame(null, (new ArrayDefinition('foo', ['img' => ['src' => ['foo']]]))->toPicture()->img()->src()->width());
    }
    
    public function testToPictureMethodImgSrcHeight()
    {
        $this->assertSame(600, (new ArrayDefinition('foo', ['img' => ['src' => [300, 600]]]))->toPicture()->img()->src()->height());
        $this->assertSame(null, (new ArrayDefinition('foo', ['img' => ['src' => [null, null]]]))->toPicture()->img()->src()->height());
        $this->assertSame(null, (new ArrayDefinition('foo', ['img' => ['src' => []]]))->toPicture()->img()->src()->height());
        $this->assertSame(null, (new ArrayDefinition('foo', ['img' => ['src' => 'foo']]))->toPicture()->img()->src()->height());
        $this->assertSame(null, (new ArrayDefinition('foo', ['img' => ['src' => [null, 'foo']]]))->toPicture()->img()->src()->height());
    }

    public function testToPictureMethodImgSrcset()
    {
        $this->assertSame(null, (new ArrayDefinition('foo', ['img' => ['src' => []]]))->toPicture()->img()->srcset());
        $this->assertSame(null, (new ArrayDefinition('foo', ['img' => ['src' => [], 'srcset' => '']]))->toPicture()->img()->srcset());
        $this->assertSame(0, (new ArrayDefinition('foo', ['img' => ['src' => [], 'srcset' => [12 => []]]]))->toPicture()->img()->srcset()?->count());
        
        $srcset = (new ArrayDefinition(name: 'foo', definition: [
            'img' => [
                'src' => [600],
                'srcset' => [
                    '480w' => [480],
                    '800w' => [800, 600],
                ],
            ],
        ]))->toPicture()->img()->srcset();
        
        $this->assertSame(2, $srcset->count());
        $this->assertSame(480, $srcset->all()[0]->width());
        $this->assertSame(null, $srcset->all()[0]->height());
        $this->assertSame('480w', $srcset->all()[0]->descriptor());
        $this->assertSame(800, $srcset->all()[1]->width());
        $this->assertSame(600, $srcset->all()[1]->height());
        $this->assertSame('800w', $srcset->all()[1]->descriptor());
    }
    
    public function testToPictureMethodImgSrcsetWithSrcObject()
    {
        $src = new Src(width: 200);
        
        $srcset = (new ArrayDefinition(name: 'foo', definition: [
            'img' => [
                'src' => [600],
                'srcset' => [
                    '480w' => $src,
                ],
            ],
        ]))->toPicture()->img()->srcset();
        
        $this->assertSame($src, $srcset->all()[0]);
    }

    public function testToPictureMethodImgAttributes()
    {
        $this->assertSame(
            [
                'alt' => 'Alternative Text',
                'class' => 'foo',
                'data-foo' => 'value',
                'sizes' => '(max-width: 600px) 480px, 800px',
                'loading' => 'lazy',
            ],
            (new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [600, 300],
                    'alt' => 'Alternative Text',
                    'class' => 'foo',
                    'data-foo' => 'value',
                    'sizes' => '(max-width: 600px) 480px, 800px',
                    'loading' => 'lazy',
                    'srcset' => [
                        '480w' => [480],
                    ],
                ],
            ]))->toPicture()->img()->attributes()
        );
    }
    
    public function testToPictureMethodSources()
    {
        $sources = (new ArrayDefinition(name: 'foo', definition: [
            'img' => [
                'src' => [900],
            ],
            'sources' => [
                [
                    'media' => '(min-width: 800px)',
                    'srcset' => [
                        '' => [1200, 600],
                    ],
                    'type' => 'image/webp',
                ],
            ],
        ]))->toPicture()->sources();

        $this->assertSame(1, $sources->count());
        
        $source = $sources->all()[0];
        $this->assertSame(['media' => '(min-width: 800px)', 'type' => 'image/webp'], $source->attributes());
        
        $srcset = $source->srcset();
        $this->assertSame(1, $srcset->count());
        $this->assertSame(1200, $srcset->all()[0]->width());
        $this->assertSame('', $srcset->all()[0]->descriptor());
    }
    
    public function testToPictureMethodSourcesWithSrcObject()
    {
        $src = new Src(width: 800);
        
        $sources = (new ArrayDefinition(name: 'foo', definition: [
            'img' => [
                'src' => [900],
            ],
            'sources' => [
                [
                    'media' => '(min-width: 800px)',
                    'srcset' => [
                        '' => $src,
                    ],
                    'type' => 'image/webp',
                ],
            ],
        ]))->toPicture()->sources();

        $this->assertSame(1, $sources->count());
        
        $source = $sources->all()[0];
        $this->assertSame(['media' => '(min-width: 800px)', 'type' => 'image/webp'], $source->attributes());
        
        $srcset = $source->srcset();
        $this->assertSame(1, $srcset->count());
        $this->assertSame($src, $srcset->all()[0]);
    }
    
    public function testToPictureMethodSourcesWithoutSrcsetWillBeSkipped()
    {
        $sources = (new ArrayDefinition(name: 'foo', definition: [
            'img' => [
                'src' => [900],
            ],
            'sources' => [
                [
                    'media' => '(min-width: 800px)',
                    'type' => 'image/webp',
                ],
            ],
        ]))->toPicture()->sources();

        $this->assertSame(0, $sources->count());
    }
    
    public function testToPictureMethodPictureAttributes()
    {
        $this->assertSame(
            [],
            (new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [900],
                ],
            ]))->toPicture()->attributes()
        );
        
        $this->assertSame(
            ['class' => 'foo'],
            (new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [900],
                ],
                'attributes' => [
                    'class' => 'foo',
                ],
            ]))->toPicture()->attributes()
        );
        
        $this->assertSame(
            [],
            (new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [900],
                ],
                'attributes' => 'invalid',
            ]))->toPicture()->attributes()
        );
    }
    
    public function testToPictureMethodPictureOptions()
    {
        $this->assertSame(
            [],
            (new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [900],
                ],
            ]))->toPicture()->options()
        );
        
        $this->assertSame(
            ['key' => 'value'],
            (new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [900],
                ],
                'options' => [
                    'key' => 'value',
                ],
            ]))->toPicture()->options()
        );
        
        $this->assertSame(
            [],
            (new ArrayDefinition(name: 'foo', definition: [
                'img' => [
                    'src' => [900],
                ],
                'options' => 'invalid',
            ]))->toPicture()->options()
        );
    }
}