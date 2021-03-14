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

interface HtmlElementInterface extends HelperInterface
{
    /**
     * Returns an HTML string
     *
     * @param string $element
     * @param array  $attribs
     * @param string $content
     * @param string $prefix
     *
     * @return string HTML string (<a href="…">Label</a>)
     */
    public function toHtml(string $element, array $attribs, string $content, string $prefix): string;
}
