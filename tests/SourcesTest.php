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
use Tobento\Service\Picture\Sources;
use Tobento\Service\Picture\SourcesInterface;
use Tobento\Service\Picture\Src;
use Tobento\Service\Picture\Srcset;

class SourcesTest extends TestCase
{
    public function testThatImplementsSourcesInterface()
    {
        $this->assertInstanceof(SourcesInterface::class, new Sources());
    }
    
    public function testAllMethod()
    {
        $this->assertSame([], (new Sources())->all());
        
        $source = new Source(srcset: new Srcset());
        $this->assertSame($source, (new Sources($source))->all()[0] ?? null);
    }
    
    public function testCountMethod()
    {
        $this->assertSame(0, (new Sources())->count());
        $this->assertSame(2, (new Sources(new Source(srcset: new Srcset()), new Source(srcset: new Srcset())))->count());
    }
    
    public function testGetIteratorMethod()
    {
        $sources = new Sources(
            new Source(srcset: new Srcset()),
            new Source(srcset: new Srcset()),
        );
        
        $iterated = [];
        
        foreach($sources as $source) {
            $iterated[] = $source;
        }
        
        $this->assertSame(2, count($iterated));
    }
    
    public function testSrcesMethod()
    {
        $sources = new Sources(
            new Source(srcset: new Srcset(new Src(width: 10))),
            new Source(srcset: new Srcset(new Src(width: 20), new Src(width: 25))),
        );
        
        $widths = [];
        
        foreach($sources->srces() as $src) {
            $widths[] = $src->width();
        }
        
        $this->assertSame([10, 20, 25], $widths);
    }
    
    public function testJsonSerializeMethod()
    {
        $sources = new Sources(
            new Source(srcset: new Srcset()),
            new Source(srcset: new Srcset()),
        );
        
        $this->assertSame(2, count($sources->jsonSerialize()));
    }
}