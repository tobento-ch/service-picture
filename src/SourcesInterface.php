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
use Generator;

/**
 * SourcesInterface
 */
interface SourcesInterface extends JsonSerializable, IteratorAggregate, Countable
{
    /**
     * Returns all sources.
     *
     * @return array<array-key, SourceInterface>
     */
    public function all(): array;

    /**
     * Returns all srces.
     *
     * @return Generator
     */
    public function srces(): Generator;
}