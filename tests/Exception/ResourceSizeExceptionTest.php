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

namespace Tobento\Service\Picture\Test\Exception;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Imager\Resource\Url;
use Tobento\Service\Picture\Exception\PictureCreateException;
use Tobento\Service\Picture\Exception\ResourceSizeException;

class ResourceSizeExceptionTest extends TestCase
{
    public function testException()
    {
        $resource = new Url('https://example.com/image.jpg');
        $e = new ResourceSizeException(
            resource: $resource,
            message: 'Message',
        );
        
        $this->assertInstanceof(PictureCreateException::class, $e);
        $this->assertSame($resource, $e->resource());
        $this->assertSame('Message', $e->getMessage());
    }
}