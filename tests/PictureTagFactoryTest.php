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
use Tobento\Service\Picture\Img;
use Tobento\Service\Picture\Picture;
use Tobento\Service\Picture\PictureTagFactory;
use Tobento\Service\Picture\PictureTagFactoryInterface;
use Tobento\Service\Picture\Source;
use Tobento\Service\Picture\Sources;
use Tobento\Service\Picture\Src;
use Tobento\Service\Picture\Srcset;

class PictureTagFactoryTest extends TestCase
{
    public function testThatImplementsPictureTagFactoryInterfaces()
    {
        $this->assertInstanceof(PictureTagFactoryInterface::class, new PictureTagFactory());
    }
    
    public function testCreateFromPictureMethodImgSrcFromUrl()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(url: 'https://example.com/image.jpg'),
            ),
            sources: new Sources(),
        );
        
        $pictureTag = (new PictureTagFactory())->createFromPicture($picture);
        
        $this->assertSame('<picture><img src="https://example.com/image.jpg"></picture>', (string)$pictureTag);
    }
    
    public function testCreateFromPictureMethodImgSrcFromPath()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(path: 'image.jpg'),
            ),
            sources: new Sources(),
        );
        
        $pictureTag = (new PictureTagFactory())->createFromPicture($picture);
        
        $this->assertSame('<picture><img src="image.jpg"></picture>', (string)$pictureTag);
    }
    
    public function testCreateFromPictureMethodImgSrcFromEncodedDataUrl()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(encoded: new Encoded(
                    encoded: 'imgdata',
                    mimeType: 'image/jpeg',
                    extension: 'jpg',
                    width: 200,
                    height: 100,
                    size: 10,
                    actions: new Actions(),
                )),
            ),
            sources: new Sources(),
        );
        
        $pictureTag = (new PictureTagFactory())->createFromPicture($picture);
        
        $this->assertSame(
            '<picture><img src="data:image/jpeg;base64,aW1nZGF0YQ==" width="200" height="100"></picture>',
            (string)$pictureTag
        );
    }
    
    public function testCreateFromPictureMethodImgSrcWithoutAnySrc()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(),
            ),
            sources: new Sources(),
        );
        
        $pictureTag = (new PictureTagFactory())->createFromPicture($picture);
        
        $this->assertSame('', (string)$pictureTag);
    }
    
    public function testCreateFromPictureMethodImgWidthAndHeightAreSetIfExists()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(path: 'image.jpg', width: 200, height: 100),
            ),
            sources: new Sources(),
        );
        
        $pictureTag = (new PictureTagFactory())->createFromPicture($picture);
        
        $this->assertSame('<picture><img src="image.jpg" width="200" height="100"></picture>', (string)$pictureTag);
    }
    
    public function testCreateFromPictureMethodImgSrcsetAreSetIfExists()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(path: 'image.jpg'),
                srcset: new Srcset(
                    new Src(path: 'img.jpg'),
                    //new Src(width: 200, descriptor: '1x'),
                ),
            ),
            sources: new Sources(),
        );
        
        $pictureTag = (new PictureTagFactory())->createFromPicture($picture);
        
        $this->assertSame('<picture><img src="image.jpg" srcset="img.jpg"></picture>', (string)$pictureTag);
    }
    
    public function testCreateFromPictureMethodImgSrcsetMultipleWithDescriptor()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(path: 'image.jpg'),
                srcset: new Srcset(
                    new Src(path: 'img1x.jpg', descriptor: '1x'),
                    new Src(path: 'img2x.jpg', descriptor: '2x', width: 200, height: 50),
                ),
            ),
            sources: new Sources(),
        );
        
        $pictureTag = (new PictureTagFactory())->createFromPicture($picture);
        
        $this->assertSame('<picture><img src="image.jpg" srcset="img1x.jpg 1x, img2x.jpg 2x"></picture>', (string)$pictureTag);
    }
    
    public function testCreateFromPictureMethodImgAttributes()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(path: 'image.jpg'),
                attributes: ['loading' => 'lazy'],
            ),
            sources: new Sources(),
        );
        
        $pictureTag = (new PictureTagFactory())->createFromPicture($picture);
        
        $this->assertSame('<picture><img loading="lazy" src="image.jpg"></picture>', (string)$pictureTag);
    }
    
    public function testCreateFromPictureMethodSources()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(path: 'image.jpg'),
            ),
            sources: new Sources(
                new Source(
                    srcset: new Srcset(
                        new Src(path: 'img.jpg'),
                    ),
                ),
                new Source(
                    srcset: new Srcset(
                        new Src(path: 'img1.jpg'),
                    ),
                ),
            ),
        );
        
        $pictureTag = (new PictureTagFactory())->createFromPicture($picture);
        
        $this->assertSame(
            '<picture><source srcset="img.jpg"><source srcset="img1.jpg"><img src="image.jpg"></picture>',
            (string)$pictureTag
        );
    }

    public function testCreateFromPictureMethodSourceWithMultipleSrcset()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(path: 'image.jpg'),
            ),
            sources: new Sources(
                new Source(
                    srcset: new Srcset(
                        new Src(path: 'img.jpg', descriptor: '1x'),
                        new Src(path: 'img.jpg', descriptor: '2x', width: 200, height: 150),
                    ),
                ),
            ),
        );
        
        $pictureTag = (new PictureTagFactory())->createFromPicture($picture);
        
        $this->assertSame('<picture><source srcset="img.jpg 1x, img.jpg 2x"><img src="image.jpg"></picture>', (string)$pictureTag);
    }
    
    public function testCreateFromPictureMethodSourceTypeIsSetFromTypeAttribute()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(path: 'image.jpg'),
            ),
            sources: new Sources(
                new Source(
                    srcset: new Srcset(
                        new Src(path: 'img.webp'),
                    ),
                    attributes: [
                        'type' => 'image/webp',
                    ],
                ),
            ),
        );
        
        $pictureTag = (new PictureTagFactory())->createFromPicture($picture);
        
        $this->assertSame(
            '<picture><source type="image/webp" srcset="img.webp"><img src="image.jpg"></picture>',
            (string)$pictureTag
        );
    }
    
    public function testCreateFromPictureMethodSourceTypeIsNotSetIfMultipleSrcsetWithDifferentMimeTypes()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(path: 'image.jpg'),
            ),
            sources: new Sources(
                new Source(
                    srcset: new Srcset(
                        new Src(path: 'img.webp', mimeType: 'image/webp'),
                        new Src(path: 'img.png', mimeType: 'image/png'),
                    ),
                    attributes: [
                        'type' => 'image/webp',
                    ],
                ),
            ),
        );
        
        $pictureTag = (new PictureTagFactory())->createFromPicture($picture);
        
        $this->assertSame(
            '<picture><source srcset="img.webp, img.png"><img src="image.jpg"></picture>',
            (string)$pictureTag
        );
    }
    
    public function testCreateFromPictureMethodSourceAttributes()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(path: 'image.jpg'),
            ),
            sources: new Sources(
                new Source(
                    srcset: new Srcset(
                        new Src(path: 'img.webp'),
                    ),
                    attributes: [
                        'media' => '(min-width: 800px)',
                        'type' => 'image/webp',
                        'width' => '300',
                        'height' => '100',
                    ],
                ),
            ),
        );
        
        $pictureTag = (new PictureTagFactory())->createFromPicture($picture);
        
        $this->assertSame(
            '<picture><source media="(min-width: 800px)" type="image/webp" width="300" height="100" srcset="img.webp"><img src="image.jpg"></picture>',
            (string)$pictureTag
        );
    }
    
    public function testCreateFromPictureMethodPictureAttributes()
    {
        $picture = new Picture(
            img: new Img(
                src: new Src(path: 'image.jpg'),
            ),
            sources: new Sources(),
            attributes: [
                'class' => 'foo',
            ],
        );
        
        $pictureTag = (new PictureTagFactory())->createFromPicture($picture);
        
        $this->assertSame('<picture class="foo"><img src="image.jpg"></picture>', (string)$pictureTag);
    }
}