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
use Mezzio\LaminasViewHelper\Helper\HtmlElementInterface;
use Mezzio\Navigation\Page\PageInterface;

use function array_diff_key;
use function array_flip;
use function array_key_exists;
use function array_merge;
use function is_string;
use function mb_strrpos;
use function mb_strtolower;
use function mb_substr;
use function trim;

final class Htmlify implements HtmlifyInterface
{
    private ?Translate $translator = null;

    private EscapeHtml $escaper;

    private HtmlElementInterface $htmlElement;

    public function __construct(EscapeHtml $escaper, HtmlElementInterface $htmlElement, ?Translate $translator = null)
    {
        $this->escaper     = $escaper;
        $this->translator  = $translator;
        $this->htmlElement = $htmlElement;
    }

    /**
     * Returns an HTML string for the given page
     *
     * @param string                $prefix             prefix to normalize the id attribute
     * @param PageInterface         $page               page to generate HTML for
     * @param bool                  $escapeLabel        Whether or not to escape the label
     * @param bool                  $addClassToListItem Whether or not to add the page class to the list item
     * @param array<string, string> $attributes
     * @param bool                  $convertToButton    Whether or not to convert a link to a button
     *
     * @return string HTML string
     */
    public function toHtml(
        string $prefix,
        PageInterface $page,
        bool $escapeLabel = true,
        bool $addClassToListItem = false,
        array $attributes = [],
        bool $convertToButton = false
    ): string {
        $label = (string) $page->getLabel();
        $title = $page->getTitle();

        if (null !== $this->translator) {
            $label = ($this->translator)($label, $page->getTextDomain());

            if (null !== $title) {
                $title = ($this->translator)($title, $page->getTextDomain());
            }
        }

        // get attribs for element

        $attributes['id']    = $page->getId();
        $attributes['title'] = $title;

        if (!$addClassToListItem) {
            $attributes['class'] = $page->getClass();
        }

        if ($convertToButton) {
            $element = 'button';
        } elseif ($page->getHref()) {
            $element              = 'a';
            $attributes['href']   = $page->getHref();
            $attributes['target'] = $page->getTarget();
        } else {
            $element = 'span';
        }

        // remove sitemap specific attributes
        $attributes = array_diff_key(
            array_merge($attributes, $page->getCustomProperties()),
            array_flip(['lastmod', 'changefreq', 'priority'])
        );

        if ($escapeLabel) {
            $label = ($this->escaper)($label);
        }

        if (array_key_exists('id', $attributes) && is_string($attributes['id'])) {
            $attributes['id'] = $this->normalizeId($prefix, $attributes['id']);
        }

        return $this->htmlElement->toHtml($element, $attributes, $label);
    }

    /**
     * Normalize an ID
     */
    private function normalizeId(string $prefix, string $value): string
    {
        $prefix = mb_strtolower(trim(mb_substr($prefix, (int) mb_strrpos($prefix, '\\')), '\\'));

        return $prefix . '-' . $value;
    }
}
