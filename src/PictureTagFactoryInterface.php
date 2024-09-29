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
 * PictureTagFactoryInterface
 */
interface PictureTagFactoryInterface
{
    /**
     * Create a new picture tag from picture.
     *
     * @param PictureInterface $picture
     * @return PictureTagInterface
     */
    public function createFromPicture(PictureInterface $picture): PictureTagInterface;
}