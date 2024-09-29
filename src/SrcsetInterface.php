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

namespace Tobento\Service\Picture;

use JsonSerializable;
use IteratorAggregate;
use Countable;

/**
 * SrcsetInterface
 */
interface SrcsetInterface extends JsonSerializable, IteratorAggregate, Countable
{
    /**
     * Returns all srces.
     *
     * @return array<array-key, SrcInterface>
     */
    public function all(): array;
}