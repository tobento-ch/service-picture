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

use Tobento\Service\Picture\Exception\PictureCreateException;
use Tobento\Service\Imager\ResourceInterface;
use Psr\Http\Message\StreamInterface;

/**
 * PictureCreatorInterface
 */
interface PictureCreatorInterface
{
    /**
     * Create a new picture from the given resource and definition.
     *
     * @param ResourceInterface $resource
     * @param DefinitionInterface $definition
     * @return CreatedPictureInterface
     * @throws PictureCreateException
     */
    public function createFromResource(ResourceInterface $resource, DefinitionInterface $definition): CreatedPictureInterface;
    
    /**
     * Create a new picture from the given stream and definition.
     *
     * @param StreamInterface $stream
     * @param DefinitionInterface $definition
     * @return CreatedPictureInterface
     * @throws PictureCreateException
     */
    public function createFromStream(StreamInterface $stream, DefinitionInterface $definition): CreatedPictureInterface;
}