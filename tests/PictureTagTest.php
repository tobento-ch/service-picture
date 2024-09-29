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
use Tobento\Service\Picture\PictureTag;
use Tobento\Service\Picture\PictureTagInterface;
use Tobento\Service\Tag\Attributes;
use Tobento\Service\Tag\Tag;
use Tobento\Service\Tag\TagInterface;

class PictureTagTest extends TestCase
{
    public function testThatImplementsInterfaces()
    {
        $picture = new PictureTag(
            new Tag(name: 'picture'),
            new Tag(name: 'img'),
        );
        
        $this->assertInstanceof(PictureTagInterface::class, $picture);
        $this->assertInstanceof(\Stringable::class, $picture);
    }
    
    public function testTagMethod()
    {
        $this->assertSame('picture', (new PictureTag(new Tag(name: 'picture'), new Tag(name: 'img')))->tag()->getName());
        $this->assertSame('picture', (new PictureTag(new Tag(name: 'foo'), new Tag(name: 'img')))->tag()->getName());
    }
    
    public function testWithTagMethod()
    {
        $picture = new PictureTag(new Tag(name: 'picture'), new Tag(name: 'img'));
        $pictureNew = $picture->withTag(new Tag(name: 'picture'));
        
        $this->assertFalse($picture === $pictureNew);
        $this->assertFalse($picture->tag() === $pictureNew->tag());
        $this->assertSame('picture', $picture->withTag(new Tag(name: 'foo'))->tag()->getName());
    }
    
    public function testImgMethod()
    {
        $this->assertSame('img', (new PictureTag(new Tag(name: 'picture'), new Tag(name: 'img')))->img()->getName());
        $this->assertSame('img', (new PictureTag(new Tag(name: 'picture'), new Tag(name: 'foo')))->img()->getName());
    }
    
    public function testWithImgMethod()
    {
        $picture = new PictureTag(new Tag(name: 'picture'), new Tag(name: 'img'));
        $pictureNew = $picture->withImg(new Tag(name: 'img'));
        
        $this->assertFalse($picture === $pictureNew);
        $this->assertFalse($picture->img() === $pictureNew->img());
        $this->assertSame('img', $picture->withImg(new Tag(name: 'foo'))->img()->getName());
    }
    
    public function testSourcesMethod()
    {
        $picture = new PictureTag(new Tag(name: 'picture'), new Tag(name: 'img'));
        
        $this->assertSame(0, count($picture->sources()));
        
        $picture = new PictureTag(
            new Tag(name: 'picture'),
            new Tag(name: 'img'),
            new Tag(name: 'source'),
            new Tag(name: 'source'),
        );
        
        $this->assertSame(2, count($picture->sources()));
    }
    
    public function testWithSourcesMethod()
    {
        $picture = new PictureTag(new Tag(name: 'picture'), new Tag(name: 'img'));
        
        $pictureNew = $picture->withSources(
            new Tag(name: 'source'),
            new Tag(name: 'source'),
        );
        
        $this->assertFalse($picture === $pictureNew);
        $this->assertFalse($picture->sources() === $pictureNew->sources());
        $this->assertSame(2, count($pictureNew->sources()));
    }
    
    public function testAttrMethod()
    {
        $picture = new PictureTag(new Tag(name: 'picture'), new Tag(name: 'img'));
        
        $this->assertSame([], $picture->tag()->attributes()->all());
        
        $pictureNew = $picture->attr('data-foo', 'value');
        
        $this->assertFalse($picture === $pictureNew);
        $this->assertSame(['data-foo' => 'value'], $pictureNew->tag()->attributes()->all());
    }
    
    public function testAttrMethodClassesAreMerged()
    {
        $picture = new PictureTag(new Tag(name: 'picture'), new Tag(name: 'img'));
        $pictureNew = $picture->attr('class', 'foo')->attr('class', 'bar');
        
        $this->assertSame(' class="foo bar"', (string)$pictureNew->tag()->attributes());
    }
    
    public function testImgAttrMethod()
    {
        $picture = new PictureTag(new Tag(name: 'picture'), new Tag(name: 'img'));
        
        $this->assertSame([], $picture->img()->attributes()->all());
        
        $pictureNew = $picture->imgAttr('data-foo', 'value');
        
        $this->assertFalse($picture === $pictureNew);
        $this->assertSame(['data-foo' => 'value'], $pictureNew->img()->attributes()->all());
    }
    
    public function testImgAttrMethodClassesAreMerged()
    {
        $picture = new PictureTag(new Tag(name: 'picture'), new Tag(name: 'img'));
        $pictureNew = $picture->imgAttr('class', 'foo')->imgAttr('class', 'bar');
        
        $this->assertSame(' class="foo bar"', (string)$pictureNew->img()->attributes());
    }
    
    public function testRenderMethod()
    {
        $picture = new PictureTag(
            new Tag(name: 'picture'),
            new Tag(name: 'img', attributes: new Attributes(['src' => 'image.jpg'])),
        );
        
        $this->assertSame('<picture><img src="image.jpg"></picture>', $picture->render());
    }
    
    public function testRenderMethodReturnsEmptyStringIfEmptySrc()
    {
        $picture = new PictureTag(new Tag(name: 'picture'), new Tag(name: 'img'));
        
        $this->assertSame('', $picture->render());
    }    

    public function testRenderMethodWithSources()
    {
        $picture = new PictureTag(
            new Tag(name: 'picture'),
            new Tag(name: 'img', attributes: new Attributes(['src' => 'image.jpg'])),
            new Tag(name: 'source'),
            new Tag(name: 'source'),
        );
        
        $this->assertSame('<picture><source><source><img src="image.jpg"></picture>', $picture->render());
    }
    
    public function testRenderMethodSourcesAreSkippedIfNotValidTag()
    {
        $picture = new PictureTag(
            new Tag(name: 'picture'),
            new Tag(name: 'img', attributes: new Attributes(['src' => 'image.jpg'])),
            new Tag(name: 'ul'),
            new Tag(name: 'div'),
        );
        
        $this->assertSame('<picture><img src="image.jpg"></picture>', $picture->render());
    }
}