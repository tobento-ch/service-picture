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
use Tobento\Service\Picture\Definition\PictureDefinition;
use Tobento\Service\Picture\DefinitionInterface;
use Tobento\Service\Picture\Img;
use Tobento\Service\Picture\Picture;
use Tobento\Service\Picture\Sources;
use Tobento\Service\Picture\Src;

class PictureDefinitionTest extends TestCase
{
    public function testThatImplementsDefinitionInterface()
    {
        $this->assertInstanceof(
            DefinitionInterface::class,
            new PictureDefinition(name: 'foo', picture: new Picture(
                img: new Img(
                    src: new Src(),
                ),
                sources: new Sources(),
            ))
        );
    }
    
    public function testNameMethod()
    {
        $this->assertSame(
            'foo',
            (new PictureDefinition(name: 'foo', picture: new Picture(
                img: new Img(
                    src: new Src(),
                ),
                sources: new Sources(),
            )))->name()
        );
    }
    
    public function testToPictureMethod()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(),
            ),
            sources: new Sources(),
        );
        
        $definition = new PictureDefinition(name: 'foo', picture: $picture);
        
        $this->assertSame($picture, $definition->toPicture());
    }
}