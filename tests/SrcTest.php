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
use Tobento\Service\Imager\Actions;
use Tobento\Service\Imager\Response\Encoded;
use Tobento\Service\Picture\Src;
use Tobento\Service\Picture\SrcInterface;

class SrcTest extends TestCase
{
    public function testThatImplementsSrcInterface()
    {
        $this->assertInstanceof(SrcInterface::class, new Src());
    }
    
    public function testWidthMethod()
    {
        $this->assertSame(null, (new Src())->width());
        $this->assertSame(10, (new Src(width: 10))->width());
    }
    
    public function testWithWidthMethod()
    {
        $src = new Src(width: 10);
        $srcNew = $src->withWidth(20);
        
        $this->assertFalse($src === $srcNew);
        $this->assertFalse($src->width() === $srcNew->width());
        $this->assertSame(null, $src->withWidth(null)->width());
    }
    
    public function testHeightMethod()
    {
        $this->assertSame(null, (new Src())->height());
        $this->assertSame(10, (new Src(height: 10))->height());
    }
    
    public function testWithHeightMethod()
    {
        $src = new Src(height: 10);
        $srcNew = $src->withHeight(20);
        
        $this->assertFalse($src === $srcNew);
        $this->assertFalse($src->height() === $srcNew->height());
        $this->assertSame(null, $src->withHeight(null)->height());
    }
    
    public function testDescriptorMethod()
    {
        $this->assertSame(null, (new Src())->descriptor());
        $this->assertSame('1x', (new Src(descriptor: '1x'))->descriptor());
    }
    
    public function testWithDescriptorMethod()
    {
        $src = new Src(descriptor: '1x');
        $srcNew = $src->withDescriptor('2x');
        
        $this->assertFalse($src === $srcNew);
        $this->assertFalse($src->descriptor() === $srcNew->descriptor());
        $this->assertSame(null, $src->withDescriptor(null)->descriptor());
    }
    
    public function testMimeTypeMethod()
    {
        $this->assertSame(null, (new Src())->mimeType());
        $this->assertSame('image/jpeg', (new Src(mimeType: 'image/jpeg'))->mimeType());
    }
    
    public function testWithMimeTypeMethod()
    {
        $src = new Src(mimeType: 'image/jpeg');
        $srcNew = $src->withMimeType('image/webp');
        
        $this->assertFalse($src === $srcNew);
        $this->assertFalse($src->mimeType() === $srcNew->mimeType());
        $this->assertSame(null, $src->withMimeType(null)->mimeType());
    }
    
    public function testUrlMethod()
    {
        $this->assertSame(null, (new Src())->url());
        $this->assertSame('url', (new Src(url: 'url'))->url());
    }
    
    public function testWithUrlMethod()
    {
        $src = new Src(url: 'url');
        $srcNew = $src->withUrl('url1');
        
        $this->assertFalse($src === $srcNew);
        $this->assertFalse($src->url() === $srcNew->url());
        $this->assertSame(null, $src->withUrl(null)->url());
    }
    
    public function testPathMethod()
    {
        $this->assertSame(null, (new Src())->path());
        $this->assertSame('path', (new Src(path: 'path'))->path());
    }
    
    public function testWithPathMethod()
    {
        $src = new Src(path: 'path');
        $srcNew = $src->withPath('path1');
        
        $this->assertFalse($src === $srcNew);
        $this->assertFalse($src->path() === $srcNew->path());
        $this->assertSame(null, $src->withPath(null)->path());
    }
    
    public function testEncodedMethod()
    {
        $encoded = new Encoded(
            encoded: 'imgdata',
            mimeType: 'image/jpeg',
            extension: 'jpg',
            width: 200,
            height: 100,
            size: 10,
            actions: new Actions(),
        );
        
        $this->assertSame(null, (new Src())->encoded());
        $this->assertSame($encoded, (new Src(encoded: $encoded))->encoded());
    }
    
    public function testWithEncodedMethod()
    {
        $encoded = new Encoded(
            encoded: 'imgdata',
            mimeType: 'image/jpeg',
            extension: 'jpg',
            width: 200,
            height: 100,
            size: 10,
            actions: new Actions(),
        );
        
        $src = new Src(encoded: $encoded);
        $srcNew = $src->withEncoded(clone $encoded);
        
        $this->assertFalse($src === $srcNew);
        $this->assertFalse($src->encoded() === $srcNew->encoded());
        $this->assertSame(null, $src->withEncoded(null)->encoded());
    }
    
    public function testOptionsMethod()
    {
        $this->assertSame([], (new Src())->options());
        $this->assertSame(['key' => 'value'], (new Src(options: ['key' => 'value']))->options());
    }
    
    public function testWithOptionsMethod()
    {
        $src = new Src(options: ['key' => 'value']);
        $srcNew = $src->withOptions(['key' => 'new']);
        
        $this->assertFalse($src === $srcNew);
        $this->assertFalse($src->options() === $srcNew->options());
    }
    
    public function testJsonSerializeMethod()
    {
        $this->assertSame(
            [
                'width' => null,
                'height' => null,
                'descriptor' => null,
                'mimeType' => null,
                'url' => null,
                'path' => null,
                'options' => [],
            ],
            (new Src())->jsonSerialize()
        );
        
        $this->assertSame(
            [
                'width' => 100,
                'height' => 50,
                'descriptor' => '1x',
                'mimeType' => 'image/webp',
                'url' => 'url',
                'path' => 'path',
                'options' => ['key' => 'value'],
            ],
            (new Src(
                width: 100,
                height: 50,
                descriptor: '1x',
                mimeType: 'image/webp',
                url: 'url',
                path: 'path',
                options: ['key' => 'value'],
            ))->jsonSerialize()
        );
    }
    
    public function testJsonSerializeMethodWithEncoded()
    {
        $this->assertSame(
            [
                'width' =>200,
                'height' => 100,
                'descriptor' => null,
                'mimeType' => 'image/jpeg',
                'url' => 'data:image/jpeg;base64,aW1nZGF0YQ==',
                'path' => null,
                'options' => [],
            ],
            (new Src(
                encoded: new Encoded(
                    encoded: 'imgdata',
                    mimeType: 'image/jpeg',
                    extension: 'jpg',
                    width: 200,
                    height: 100,
                    size: 10,
                    actions: new Actions(),
                ),
                width: 30,
                height: 50,
                mimeType: 'image/webp',
            ))->jsonSerialize()
        );
    }    
}