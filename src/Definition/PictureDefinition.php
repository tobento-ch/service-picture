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

namespace Tobento\Service\Picture\Definition;

use Tobento\Service\Picture\DefinitionInterface;
use Tobento\Service\Picture\PictureInterface;

/**
 * PictureDefinition
 */
class PictureDefinition implements DefinitionInterface
{
    /**
     * Create a new PictureDefinition.
     *
     * @param string $name
     * @param PictureInterface $picture
     */
    public function __construct(
        protected string $name,
        protected PictureInterface $picture,
    ) {}
    
    /**
     * Returns a definition name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * To picture.
     *
     * @return PictureInterface
     */
    public function toPicture(): PictureInterface
    {
        return $this->picture;
    }
}