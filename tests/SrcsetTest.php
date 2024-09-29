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
use Tobento\Service\Picture\Src;
use Tobento\Service\Picture\Srcset;
use Tobento\Service\Picture\SrcsetInterface;

class SrcsetTest extends TestCase
{
    public function testThatImplementsSrcsetInterface()
    {
        $this->assertInstanceof(SrcsetInterface::class, new Srcset());
    }

    public function testAllMethod()
    {
        $this->assertSame([], (new Srcset())->all());
        
        $src = new Src();
        $this->assertSame($src, (new Srcset($src))->all()[0] ?? null);
    }
    
    public function testCountMethod()
    {
        $this->assertSame(0, (new Srcset())->count());
        $this->assertSame(2, (new Srcset(new Src(), new Src()))->count());
    }
    
    public function testGetIteratorMethod()
    {
        $srcset = new Srcset(
            new Src(),
            new Src(),
        );
        
        $iterated = [];
        
        foreach($srcset as $src) {
            $iterated[] = $src;
        }
        
        $this->assertSame(2, count($iterated));
    }
    
    public function testJsonSerializeMethod()
    {
        $srcset = new Srcset(
            new Src(),
            new Src(),
        );
        
        $this->assertSame(2, count($srcset->jsonSerialize()));
    }
}