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

use Tobento\Service\Tag\TagInterface;
use Stringable;

/**
 * SrcsetInterface
 */
interface PictureTagInterface extends Stringable
{
    /**
     * Returns the picture tag.
     *
     * @return TagInterface
     */
    public function tag(): TagInterface;
    
    /**
     * Returns a new instance with the given picture tag.
     *
     * @param TagInterface $tag
     * @return static
     */
    public function withTag(TagInterface $tag): static;
    
    /**
     * Returns the img.
     *
     * @return TagInterface
     */
    public function img(): TagInterface;
    
    /**
     * Returns a new instance with the given img tag.
     *
     * @param TagInterface $img
     * @return static
     */
    public function withImg(TagInterface $img): static;
    
    /**
     * Returns the sources.
     *
     * @return array<array-key, TagInterface>
     */
    public function sources(): array;
    
    /**
     * Returns a new instance with the given source tags.
     *
     * @param TagInterface ...$source
     * @return static
     */
    public function withSources(TagInterface ...$source): static;
    
    /**
     * Returns a new instance with the specified attribute.
     * Class must add to existing classes if a string is provided,
     * otherwise overwrite.     
     *
     * @param string $name
     * @param mixed $value
     * @return static
     */
    public function attr(string $name, mixed $value = null): static;
    
    /**
     * Returns a new instance with the specified attribute.
     * Class must add to existing classes if a string is provided,
     * otherwise overwrite.     
     *
     * @param string $name
     * @param mixed $value
     * @return static
     */
    public function imgAttr(string $name, mixed $value = null): static;
    
    /**
     * Returns the evaluated html of the icon. Must be escaped.
     *
     * @return string
     */
    public function render(): string;
}