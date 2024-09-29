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
use Stringable;
use Generator;

/**
 * PictureInterface
 */
interface PictureInterface extends JsonSerializable, Stringable
{
    /**
     * Returns the img.
     *
     * @return ImgInterface
     */
    public function img(): ImgInterface;
    
    /**
     * Returns a new instance with the given img.
     *
     * @param ImgInterface $img
     * @return static
     */
    public function withImg(ImgInterface $img): static;
    
    /**
     * Returns the sources.
     *
     * @return SourcesInterface
     */
    public function sources(): SourcesInterface;
    
    /**
     * Returns a new instance with the given sources.
     *
     * @param SourcesInterface $sources
     * @return static
     */
    public function withSources(SourcesInterface $sources): static;
    
    /**
     * Returns the attributes.
     *
     * @return array
     */
    public function attributes(): array;
    
    /**
     * Returns a new instance with the given attributes.
     *
     * @param array $attributes
     * @return static
     */
    public function withAttributes(array $attributes): static;
    
    /**
     * Returns the options.
     *
     * @return array
     */
    public function options(): array;
    
    /**
     * Returns a new instance with the given options.
     *
     * @param array $options
     * @return static
     */
    public function withOptions(array $options): static;
    
    /**
     * Returns the srces.
     *
     * @return Generator
     */
    public function srces(): Generator;
    
    /**
     * Returns a new created picture tag.
     *
     * @return PictureTagInterface
     */
    public function toTag(): PictureTagInterface;
}