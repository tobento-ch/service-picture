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
use Tobento\Service\Picture\NullPictureTag;
use Tobento\Service\Picture\PictureTagInterface;
use Tobento\Service\Tag\Attributes;
use Tobento\Service\Tag\Tag;
use Tobento\Service\Tag\TagInterface;

class NullNullPictureTagTest extends TestCase
{
    public function testThatImplementsInterfaces()
    {
        $picture = new NullPictureTag();
        
        $this->assertInstanceof(PictureTagInterface::class, $picture);
        $this->assertInstanceof(\Stringable::class, $picture);
    }
    
    public function testTagMethod()
    {
        $this->assertSame('picture', (new NullPictureTag())->tag()->getName());
    }
    
    public function testWithTagMethod()
    {
        $picture = new NullPictureTag(new NullPictureTag());
        $pictureNew = $picture->withTag(new Tag(name: 'picture'));
        
        $this->assertFalse($picture === $pictureNew);
        $this->assertFalse($picture->tag() === $pictureNew->tag());
        $this->assertSame('picture', $picture->withTag(new Tag(name: 'foo'))->tag()->getName());
    }
    
    public function testImgMethod()
    {
        $this->assertSame('img', (new NullPictureTag())->img()->getName());
    }
    
    public function testWithImgMethod()
    {
        $picture = new NullPictureTag();
        $pictureNew = $picture->withImg(new Tag(name: 'img'));
        
        $this->assertFalse($picture === $pictureNew);
        $this->assertFalse($picture->img() === $pictureNew->img());
        $this->assertSame('img', $picture->withImg(new Tag(name: 'foo'))->img()->getName());
    }
    
    public function testSourcesMethod()
    {
        $picture = new NullPictureTag();
        
        $this->assertSame(0, count($picture->sources()));
    }
    
    public function testWithSourcesMethod()
    {
        $picture = new NullPictureTag();
        
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
        $picture = new NullPictureTag();
        
        $this->assertSame([], $picture->tag()->attributes()->all());
        
        $pictureNew = $picture->attr('data-foo', 'value');
        
        $this->assertFalse($picture === $pictureNew);
        $this->assertSame(['data-foo' => 'value'], $pictureNew->tag()->attributes()->all());
    }
    
    public function testImgAttrMethod()
    {
        $picture = new NullPictureTag();
        
        $this->assertSame([], $picture->img()->attributes()->all());
        
        $pictureNew = $picture->imgAttr('data-foo', 'value');
        
        $this->assertFalse($picture === $pictureNew);
        $this->assertSame(['data-foo' => 'value'], $pictureNew->img()->attributes()->all());
    }
    
    public function testRenderMethod()
    {
        $picture = new NullPictureTag();
        
        $this->assertSame('', $picture->render());
    }
}