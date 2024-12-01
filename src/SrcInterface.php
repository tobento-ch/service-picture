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

use Tobento\Service\Imager\Response\Encoded;
use JsonSerializable;

/**
 * SrcInterface
 */
interface SrcInterface extends JsonSerializable
{
    /**
     * Returns the width.
     *
     * @return null|int
     */
    public function width(): null|int;
    
    /**
     * Returns a new instance with the given width.
     *
     * @param null|int $width
     * @return static
     */
    public function withWidth(null|int $width): static;
    
    /**
     * Returns the height.
     *
     * @return null|int
     */
    public function height(): null|int;
    
    /**
     * Returns a new instance with the given height.
     *
     * @param null|int $height
     * @return static
     */
    public function withHeight(null|int $height): static;
    
    /**
     * Returns the descriptor.
     *
     * @return null|string
     */
    public function descriptor(): null|string;
    
    /**
     * Returns a new instance with the given descriptor.
     *
     * @param null|string $descriptor
     * @return static
     */
    public function withDescriptor(null|string $descriptor): static;
    
    /**
     * Returns the mime type.
     *
     * @return null|string
     */
    public function mimeType(): null|string;
    
    /**
     * Returns a new instance with the given mime type.
     *
     * @param null|string $mimeType
     * @return static
     */
    public function withMimeType(null|string $mimeType): static;
    
    /**
     * Returns the url.
     *
     * @return null|string
     */
    public function url(): null|string;
    
    /**
     * Returns a new instance with the given url.
     *
     * @param null|string $url
     * @return static
     */
    public function withUrl(null|string $url): static;
    
    /**
     * Returns the path.
     *
     * @return null|string
     */
    public function path(): null|string;
    
    /**
     * Returns a new instance with the given path.
     *
     * @param null|string $path
     * @return static
     */
    public function withPath(null|string $path): static;
    
    /**
     * Returns the encoded.
     *
     * @return null|Encoded
     */
    public function encoded(): null|Encoded;
    
    /**
     * Returns a new instance with the given encoded.
     *
     * @param null|Encoded $encoded
     * @return static
     */
    public function withEncoded(null|Encoded $encoded): static;
    
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
}