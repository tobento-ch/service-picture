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
 * DefinitionInterface
 */
interface DefinitionInterface
{
    /**
     * Returns a definition name.
     *
     * @return string
     */
    public function name(): string;
    
    /**
     * To picture.
     *
     * @return PictureInterface
     */
    public function toPicture(): PictureInterface;
}