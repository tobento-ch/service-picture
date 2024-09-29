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
use Tobento\Service\Tag\Tag;

/**
 * NullPictureTag
 */
class NullPictureTag extends PictureTag
{
    /**
     * @var TagInterface
     */
    protected TagInterface $tag;
    
    /**
     * @var TagInterface
     */
    protected TagInterface $img;
    
    /**
     * Create a new NullPictureTag.
     */
    public function __construct()
    {
        $this->tag = new Tag(name: 'picture');
        $this->img = new Tag(name: 'img');
    }
    
    /**
     * Returns the evaluated html of the icon. Must be escaped.
     *
     * @return string
     */
    public function render(): string
    {
        return '';
    }
}