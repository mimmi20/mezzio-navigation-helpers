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

use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;
use Mezzio\Navigation\Page\PageInterface;

final class Htmlify implements HtmlifyInterface
{
    /** @var Translate|null */
    private $translator;

    /** @var EscapeHtml */
    private $escaper;

    /** @var \Mezzio\Navigation\Helper\HtmlElementInterface */
    private $htmlElement;

    /**
     * @param \Laminas\View\Helper\EscapeHtml                $escaper
     * @param \Mezzio\Navigation\Helper\HtmlElementInterface $htmlElement
     * @param \Laminas\I18n\View\Helper\Translate|null       $translator
     */
    public function __construct(EscapeHtml $escaper, HtmlElementInterface $htmlElement, ?Translate $translator = null)
    {
        $this->escaper     = $escaper;
        $this->translator  = $translator;
        $this->htmlElement = $htmlElement;
    }

    /**
     * Returns an HTML string containing an 'a' element for the given page
     *
     * @param string        $prefix
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
    ): string {
        $label = (string) $page->getLabel();
        $title = (string) $page->getTitle();

        if (null !== $this->translator) {
            $label = ($this->translator)($label, $page->getTextDomain());
            $title = ($this->translator)($title, $page->getTextDomain());
        }

        // get attribs for element
        $attribs = array_merge(
            $attributes,
            [
                'id' => $page->getId(),
                'title' => $title,
            ]
        );

        if (false === $addClassToListItem) {
            $attribs['class'] = $page->getClass();
        }

        // does page have a href?
        $href = $page->getHref();

        if ($href) {
            $element           = 'a';
            $attribs['href']   = $href;
            $attribs['target'] = $page->getTarget();
        } else {
            $element = 'span';
        }

        // remove sitemap specific attributes
        $attribs = array_diff_key(
            array_merge($attribs, $page->getCustomProperties()),
            array_flip(['lastmod', 'changefreq', 'priority'])
        );

        if (true === $escapeLabel) {
            $label = ($this->escaper)($label);
        }

        return $this->htmlElement->toHtml($element, $attribs, $label, $prefix);
    }
}
