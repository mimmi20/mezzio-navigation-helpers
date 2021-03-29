<?php
/**
 * This file is part of the mimmi20/mezzio-navigation-helpers package.
 *
 * Copyright (c) 2021, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Mezzio\Navigation\Helper;

use Mezzio\Navigation\Page\PageInterface;

interface HtmlifyInterface extends HelperInterface
{
    /**
     * Returns an HTML string containing an 'a' element for the given page
     *
     * @param PageInterface $page               page to generate HTML for
     * @param bool          $escapeLabel        Whether or not to escape the label
     * @param bool          $addClassToListItem Whether or not to add the page class to the list item
     * @param string[]      $attributes
     *
     * @return string HTML string (<a href="…">Label</a>)
     */
    public function toHtml(
        string $prefix,
        PageInterface $page,
        bool $escapeLabel = true,
        bool $addClassToListItem = false,
        array $attributes = []
    ): string;
}
