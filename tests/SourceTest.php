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
use Tobento\Service\Picture\Source;
use Tobento\Service\Picture\SourceInterface;
use Tobento\Service\Picture\Srcset;

class SourceTest extends TestCase
{
    public function testThatImplementsSourceInterface()
    {
        $this->assertInstanceof(SourceInterface::class, new Source(srcset: new Srcset()));
    }
    
    public function testSrcsetMethod()
    {
        $srcset = new Srcset();
        $this->assertSame($srcset, (new Source(srcset: $srcset))->srcset());
    }
    
    public function testWithSrcsetMethod()
    {
        $source = new Source(srcset: new Srcset());
        $sourceNew = $source->withSrcset(new Srcset());
        
        $this->assertFalse($source === $sourceNew);
        $this->assertFalse($source->srcset() === $sourceNew->srcset());
    }
    
    public function testAttributesMethod()
    {
        $this->assertSame([], (new Source(srcset: new Srcset()))->attributes());
        $this->assertSame(['key' => 'value'], (new Source(srcset: new Srcset(), attributes: ['key' => 'value']))->attributes());
    }
    
    public function testWithAttributesMethod()
    {
        $source = new Source(srcset: new Srcset(), attributes: ['key' => 'value']);
        $sourceNew = $source->withAttributes(attributes: ['key' => 'foo']);
        
        $this->assertFalse($source === $sourceNew);
        $this->assertFalse($source->attributes() === $sourceNew->attributes());
        $this->assertSame(['key' => 'foo'], $sourceNew->attributes());
    }
    
    public function testJsonSerializeMethod()
    {
        $source = new Source(srcset: new Srcset(), attributes: ['key' => 'value']);
        $serialized = $source->jsonSerialize();
        
        $this->assertTrue(is_array($serialized['srcset'] ?? null));
        $this->assertSame(['key' => 'value'], $serialized['attributes'] ?? null);
    }
}