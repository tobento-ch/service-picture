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

class PictureCreateExceptionTest extends TestCase
{
    public function testException()
    {
        $resource = new Url('https://example.com/image.jpg');
        $e = new PictureCreateException(
            resource: $resource,
            message: 'Message',
        );
        
        $this->assertSame($resource, $e->resource());
        $this->assertSame('Message', $e->getMessage());
    }
}