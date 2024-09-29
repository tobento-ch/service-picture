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
use Tobento\Service\Picture\ImgInterface;
use Tobento\Service\Picture\Src;
use Tobento\Service\Picture\Srcset;

class ImgTest extends TestCase
{
    public function testThatImplementsImgInterface()
    {
        $this->assertInstanceof(ImgInterface::class, new Img(src: new Src()));
    }
    
    public function testSrcMethod()
    {
        $src = new Src();
        
        $this->assertSame($src, (new Img(src: $src))->src());
    }
    
    public function testWithSrcMethod()
    {
        $img = new Img(src: new Src());
        $imgNew = $img->withSrc(new Src());
        
        $this->assertFalse($img === $imgNew);
        $this->assertFalse($img->src() === $imgNew->src());
    }
    
    public function testSrcsetMethod()
    {
        $this->assertSame(null, (new Img(src: new Src()))->srcset());
        
        $srcset = new Srcset();
        $this->assertSame($srcset, (new Img(src: new Src(), srcset: $srcset))->srcset());
    }
    
    public function testWithSrcsetMethod()
    {
        $img = new Img(src: new Src(), srcset: new Srcset());
        $imgNew = $img->withSrcset(new Srcset());
        
        $this->assertFalse($img === $imgNew);
        $this->assertFalse($img->srcset() === $imgNew->srcset());
        $this->assertSame(null, $img->withSrcset(null)->srcset());
    }
    
    public function testAttributesMethod()
    {
        $this->assertSame([], (new Img(src: new Src()))->attributes());
        $this->assertSame(['key' => 'value'], (new Img(src: new Src(), attributes: ['key' => 'value']))->attributes());
    }
    
    public function testWithAttributesMethod()
    {
        $img = new Img(src: new Src(), attributes: ['key' => 'value']);
        $imgNew = $img->withAttributes(attributes: ['key' => 'foo']);
        
        $this->assertFalse($img === $imgNew);
        $this->assertFalse($img->attributes() === $imgNew->attributes());
        $this->assertSame(['key' => 'foo'], $imgNew->attributes());
    }
    
    public function testJsonSerializeMethod()
    {
        $img = new Img(src: new Src(), attributes: ['key' => 'value']);
        $serialized = $img->jsonSerialize();
        
        $this->assertTrue(is_array($serialized['src'] ?? null));
        $this->assertSame(null, $serialized['srcset'] ?? null);
        $this->assertSame(['key' => 'value'], $serialized['attributes'] ?? null);
    }
    
    public function testJsonSerializeMethodWithSrcset()
    {
        $img = new Img(src: new Src(), srcset: new Srcset());
        $serialized = $img->jsonSerialize();
        
        $this->assertTrue(is_array($serialized['srcset'] ?? null));
    }
}