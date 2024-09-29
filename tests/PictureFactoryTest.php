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
use Tobento\Service\Picture\PictureFactory;
use Tobento\Service\Picture\PictureFactoryInterface;
use Tobento\Service\Picture\PictureInterface;

class PictureFactoryTest extends TestCase
{
    public function testThatImplementsPictureFactoryInterfaces()
    {
        $this->assertInstanceof(PictureFactoryInterface::class, new PictureFactory());
    }
    
    public function testCreateFromArrayMethodImgSrc()
    {
        $src = [
            'width' => 300,
            'height' => null,
            'descriptor' => null,
            'mimeType' => null,
            'url' => null,
            'path' => null,
            'options' => [],
        ];
        
        $picture = (new PictureFactory())->createFromArray([
            'img' => [
                'src' => $src,
            ],
        ]);
        
        $this->assertSame($src, $picture->img()->src()->jsonSerialize());
    }
    
    public function testCreateFromArrayMethodImgSrcWithWrongParametersAreSkipped()
    {
        $src = [
            'width' => null,
            'height' => null,
            'descriptor' => null,
            'mimeType' => null,
            'url' => null,
            'path' => null,
            'options' => [],
        ];
        
        $picture = (new PictureFactory())->createFromArray([
            'img' => [
                'src' => ['invalid' => 'ddd'],
            ],
        ]);
        
        $this->assertSame($src, $picture->img()->src()->jsonSerialize());
    }
    
    public function testCreateFromArrayMethodImgSrcWithWrongParametersThowsIfDefined()
    {
        $this->expectException(\Error::class);
        
        $picture = (new PictureFactory(throwOnError: true))->createFromArray([
            'img' => [
                'src' => ['invalid' => 'ddd'],
            ],
        ]);
        
        $this->assertSame($src, $picture->img()->src()->jsonSerialize());
    }    
    
    public function testCreateFromArrayMethodWithoutImgSrc()
    {
        $src = [
            'width' => null,
            'height' => null,
            'descriptor' => null,
            'mimeType' => null,
            'url' => null,
            'path' => null,
            'options' => [],
        ];

        $picture = (new PictureFactory())->createFromArray([]);
        $this->assertSame($src, $picture->img()->src()->jsonSerialize());
        
        $picture = (new PictureFactory())->createFromArray(['img' => []]);
        $this->assertSame($src, $picture->img()->src()->jsonSerialize());
    }
    
    public function testCreateFromArrayMethodImgSrcset()
    {
        $srcset = [
            [
                'width' => 300,
                'height' => null,
                'descriptor' => null,
                'mimeType' => null,
                'url' => null,
                'path' => null,
                'options' => [],
            ],
        ];
        
        $picture = (new PictureFactory())->createFromArray([
            'img' => [
                'srcset' => $srcset,
            ],
        ]);
        
        $this->assertSame($srcset, $picture->img()->srcset()->jsonSerialize());
    }
    
    public function testCreateFromArrayMethodImgSrcsetWithWrongParametersAreSkipped()
    {
        $picture = (new PictureFactory())->createFromArray([
            'img' => [
                'srcset' => [['width' => 200], ['invalid' => 'foo']],
            ],
        ]);
        
        $this->assertSame(1, $picture->img()->srcset()->count());
    }
    
    public function testCreateFromArrayMethodImgSrcsetWithWrongParametersThrowsIfDefined()
    {
        $this->expectException(\Error::class);
        
        $picture = (new PictureFactory(throwOnError: true))->createFromArray([
            'img' => [
                'srcset' => [['width' => 200], ['invalid' => 'foo']],
            ],
        ]);
    }
    
    public function testCreateFromArrayMethodImgAttributes()
    {
        $picture = (new PictureFactory())->createFromArray([
            'img' => [
                'attributes' => ['key' => 'value'],
            ],
        ]);
        
        $this->assertSame(['key' => 'value'], $picture->img()->attributes());
    }
    
    public function testCreateFromArrayMethodSources()
    {
        $sources = [
            [
                'srcset' => [
                    [
                        'width' => 200,
                        'height' => 400,
                        'descriptor' => '2x',
                        'mimeType' => 'image/png',
                        'url' => null,
                        'path' => null,
                        'options' => [],
                    ],
                ],
                'attributes' => ['key' => 'value']
            ],
        ];
        
        $picture = (new PictureFactory())->createFromArray([
            'sources' => $sources,
        ]);
        
        $this->assertSame($sources, $picture->sources()->jsonSerialize());
    }
    
    public function testCreateFromArrayMethodSourcesWithWrongParametersAreSkipped()
    {
        $sources = [
            [
                'srcset' => [
                    [
                        'invalid' => 'ffff',
                    ],
                ],
            ],
        ];
        
        $picture = (new PictureFactory())->createFromArray([
            'sources' => $sources,
        ]);
        
        $this->assertSame(0, $picture->sources()->all()[0]->srcset()->count());
    }
    
    public function testCreateFromArrayMethodSourcesWithWrongParametersThrowsIfDefined()
    {
        $this->expectException(\Error::class);
        
        $sources = [
            [
                'srcset' => [
                    [
                        'invalid' => 'ffff',
                    ],
                ],
            ],
        ];
        
        $picture = (new PictureFactory(throwOnError: true))->createFromArray([
            'sources' => $sources,
        ]);
    }
    
    public function testCreateFromArrayMethodPictureAttributes()
    {
        $picture = (new PictureFactory())->createFromArray([
            'attributes' => ['key' => 'value'],
        ]);
        
        $this->assertSame(['key' => 'value'], $picture->attributes());
    }
    
    public function testCreateFromArrayMethodPictureOptions()
    {
        $picture = (new PictureFactory())->createFromArray([
            'options' => ['key' => 'value'],
        ]);
        
        $this->assertSame(['key' => 'value'], $picture->options());
    }
}