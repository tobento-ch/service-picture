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

/**
 * Src
 */
class Src implements SrcInterface
{
    /**
     * Create a new Src.
     *
     * @param null|int $width
     * @param null|int $height
     * @param null|string $descriptor
     * @param null|string $mimeType
     * @param null|string $url
     * @param null|string $path
     * @param null|Encoded $encoded
     * @param array $options
     */
    public function __construct(
        protected null|int $width = null,
        protected null|int $height = null,
        protected null|string $descriptor = null,
        protected null|string $mimeType = null,
        protected null|string $url = null,
        protected null|string $path = null,
        protected null|Encoded $encoded = null,
        protected array $options = [],
    ) {}

    /**
     * Returns the width.
     *
     * @return null|int
     */
    public function width(): null|int
    {
        return $this->width;
    }
    
    /**
     * Returns a new instance with the given width.
     *
     * @param null|int $width
     * @return static
     */
    public function withWidth(null|int $width): static
    {
        $new = clone $this;
        $new->width = $width;
        return $new;
    }
    
    /**
     * Returns the height.
     *
     * @return null|int
     */
    public function height(): null|int
    {
        return $this->height;
    }
    
    /**
     * Returns a new instance with the given height.
     *
     * @param null|int $height
     * @return static
     */
    public function withHeight(null|int $height): static
    {
        $new = clone $this;
        $new->height = $height;
        return $new;
    }
    
    /**
     * Returns the descriptor.
     *
     * @return null|string
     */
    public function descriptor(): null|string
    {
        return $this->descriptor;
    }
    
    /**
     * Returns a new instance with the given descriptor.
     *
     * @param null|string $descriptor
     * @return static
     */
    public function withDescriptor(null|string $descriptor): static
    {
        $new = clone $this;
        $new->descriptor = $descriptor;
        return $new;
    }
    
    /**
     * Returns the mime type.
     *
     * @return null|string
     */
    public function mimeType(): null|string
    {
        return $this->mimeType;
    }
    
    /**
     * Returns a new instance with the given mime type.
     *
     * @param null|string $mimeType
     * @return static
     */
    public function withMimeType(null|string $mimeType): static
    {
        $new = clone $this;
        $new->mimeType = $mimeType;
        return $new;
    }
    
    /**
     * Returns the url.
     *
     * @return null|string
     */
    public function url(): null|string
    {
        return $this->url;
    }
    
    /**
     * Returns a new instance with the given url.
     *
     * @param null|string $url
     * @return static
     */
    public function withUrl(null|string $url): static
    {
        $new = clone $this;
        $new->url = $url;
        return $new;
    }
    
    /**
     * Returns the path.
     *
     * @return null|string
     */
    public function path(): null|string
    {
        return $this->path;
    }
    
    /**
     * Returns a new instance with the given path.
     *
     * @param null|string $path
     * @return static
     */
    public function withPath(null|string $path): static
    {
        $new = clone $this;
        $new->path = $path;
        return $new;
    }
    
    /**
     * Returns the encoded.
     *
     * @return null|Encoded
     */
    public function encoded(): null|Encoded
    {
        return $this->encoded;
    }
    
    /**
     * Returns a new instance with the given encoded.
     *
     * @param null|Encoded $encoded
     * @return static
     */
    public function withEncoded(null|Encoded $encoded): static
    {
        $new = clone $this;
        $new->encoded = $encoded;
        return $new;
    }
    
    /**
     * Returns the options.
     *
     * @return array
     */
    public function options(): array
    {
        return $this->options;
    }
    
    /**
     * Returns a new instance with the given options.
     *
     * @param array $options
     * @return static
     */
    public function withOptions(array $options): static
    {
        $new = clone $this;
        $new->options = $options;
        return $new;
    }
    
    /**
     * Serializes the object to a value that can be serialized natively by json_encode().
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = [
            'width' => $this->width(),
            'height' => $this->height(),
            'descriptor' => $this->descriptor(),
            'mimeType' => $this->mimeType(),
            'url' => $this->url(),
            'path' => $this->path(),
            'options' => $this->options(),
        ];
        
        if (!is_null($this->encoded())) {
            $data['url'] = $this->encoded()->dataUrl();
            $data['mimeType'] = $this->encoded()->mimeType();
            $data['width'] = $this->encoded()->width();
            $data['height'] = $this->encoded()->height();
        }
        
        return $data;
    }
}