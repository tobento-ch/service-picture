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

/**
 * PictureInterface
 */
interface PictureFactoryInterface
{
    /**
     * Create a new picture from array.
     *
     * @param array $picture
     * @return PictureInterface
     */
    public function createFromArray(array $picture): PictureInterface;
}