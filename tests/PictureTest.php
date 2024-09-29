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

namespace Tobento\Service\Picture\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Picture\Img;
use Tobento\Service\Picture\Picture;
use Tobento\Service\Picture\PictureInterface;
use Tobento\Service\Picture\PictureTagInterface;
use Tobento\Service\Picture\Source;
use Tobento\Service\Picture\Sources;
use Tobento\Service\Picture\Src;
use Tobento\Service\Picture\Srcset;

class PictureTest extends TestCase
{
    public function testThatImplementsInterfaces()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(),
            ),
            sources: new Sources(),
        );
        
        $this->assertInstanceof(PictureInterface::class, $picture);
        $this->assertInstanceof(\Stringable::class, $picture);
    }
    
    public function testImgMethod()
    {
        $img = new Img(src: new Src());
        $picture = new Picture(img: $img, sources: new Sources());
        
        $this->assertSame($img, $picture->img());
    }
    
    public function testWithImgMethod()
    {
        $picture = new Picture(img: new Img(src: new Src()), sources: new Sources());
        $pictureNew = $picture->withImg(new Img(src: new Src()));
        
        $this->assertFalse($picture === $pictureNew);
        $this->assertFalse($picture->img() === $pictureNew->img());
    }
    
    public function testSourcesMethod()
    {
        $sources = new Sources();
        $picture = new Picture(img: new Img(src: new Src()), sources: $sources);
        
        $this->assertSame($sources, $picture->sources());
    }
    
    public function testWithSourcesMethod()
    {
        $picture = new Picture(img: new Img(src: new Src()), sources: new Sources());
        $pictureNew = $picture->withSources(new Sources());
        
        $this->assertFalse($picture === $pictureNew);
        $this->assertFalse($picture->sources() === $pictureNew->sources());
    }
    
    public function testAttributesMethod()
    {
        $this->assertSame(
            [],
            (new Picture(img: new Img(src: new Src()), sources: new Sources()))->attributes()
        );
        
        $this->assertSame(
            ['key' => 'value'],
            (new Picture(img: new Img(src: new Src()), sources: new Sources(), attributes: ['key' => 'value']))->attributes()
        );
    }
    
    public function testWithAttributesMethod()
    {
        $picture = new Picture(img: new Img(src: new Src()), sources: new Sources(), attributes: ['key' => 'value']);
        $pictureNew = $picture->withAttributes(['foo' => 'bar']);
        
        $this->assertFalse($picture === $pictureNew);
        $this->assertFalse($picture->attributes() === $pictureNew->attributes());
    }
    
    public function testOptionsMethod()
    {
        $this->assertSame(
            [],
            (new Picture(img: new Img(src: new Src()), sources: new Sources()))->options()
        );
        
        $this->assertSame(
            ['key' => 'value'],
            (new Picture(img: new Img(src: new Src()), sources: new Sources(), options: ['key' => 'value']))->options()
        );
    }
    
    public function testWithOptionsMethod()
    {
        $picture = new Picture(img: new Img(src: new Src()), sources: new Sources(), options: ['key' => 'value']);
        $pictureNew = $picture->withOptions(['foo' => 'bar']);
        
        $this->assertFalse($picture === $pictureNew);
        $this->assertFalse($picture->options() === $pictureNew->options());
    }
    
    public function testSrcesMethod()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(width: 10),
                srcset: new Srcset(
                    new Src(width: 20),
                ),
            ),
            sources: new Sources(
                new Source(
                    srcset: new Srcset(new Src(width: 30)),
                ),
            ),
        );
        
        $widths = [];
        
        foreach($picture->srces() as $src) {
            $widths[] = $src->width();
        }
        
        $this->assertSame([10, 20, 30], $widths);
    }
    
    public function testToTagMethod()
    {
        $picture = new Picture(img: new Img(src: new Src()), sources: new Sources());
        
        $this->assertInstanceof(PictureTagInterface::class, $picture->toTag());
    }
    
    public function testJsonSerializeMethod()
    {
        $picture = new Picture(
            img: new Img(src: new Src()),
            sources: new Sources(),
            attributes: ['key' => 'foo'],
            options: ['key' => 'bar'],
        );
        $serialized = $picture->jsonSerialize();
        
        $this->assertTrue(is_array($serialized['img'] ?? null));
        $this->assertTrue(is_array($serialized['sources'] ?? null));
        $this->assertSame(['key' => 'foo'], $serialized['attributes'] ?? null);
        $this->assertSame(['key' => 'bar'], $serialized['options'] ?? null);
    }
    
    public function testToStringMethod()
    {
        $picture = new Picture(img: new Img(src: new Src(url: 'url')), sources: new Sources());
        
        $this->assertSame('<picture><img src="url"></picture>', $picture->__toString());
    }
}