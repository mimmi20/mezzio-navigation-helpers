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

use Laminas\Json\Json;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;

final class HtmlElement implements HtmlElementInterface
{
    /** @var EscapeHtml */
    private $escaper;

    /** @var EscapeHtmlAttr */
    private $escapeHtmlAttr;

    /**
     * @param \Laminas\View\Helper\EscapeHtml     $escaper
     * @param \Laminas\View\Helper\EscapeHtmlAttr $escapeHtmlAttr
     */
    public function __construct(EscapeHtml $escaper, EscapeHtmlAttr $escapeHtmlAttr)
    {
        $this->escaper        = $escaper;
        $this->escapeHtmlAttr = $escapeHtmlAttr;
    }

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
    public function toHtml(string $element, array $attribs, string $content, string $prefix): string
    {
        return '<' . $element . $this->htmlAttribs($prefix, $attribs) . '>' . $content . '</' . $element . '>';
    }

    /**
     * Converts an associative array to a string of tag attributes.
     *
     * @param string $prefix
     * @param array  $attribs an array where each key-value pair is converted
     *                        to an attribute name and value
     *
     * @return string
     */
    private function htmlAttribs(string $prefix, array $attribs): string
    {
        // filter out empty string values
        $attribs = array_filter(
            $attribs,
            static fn ($value): bool => null !== $value && (!is_string($value) || mb_strlen($value))
        );

        $xhtml = '';

        foreach ($attribs as $key => $val) {
            $key = ($this->escaper)($key);

            if (true === $val) {
                $xhtml .= sprintf(' %s', $key);

                continue;
            }

            if (0 === mb_strpos($key, 'on') || ('constraints' === $key)) {
                // Don't escape event attributes; _do_ substitute double quotes with singles
                if (!is_scalar($val)) {
                    // non-scalar data should be cast to JSON first
                    $val = Json::encode($val);
                }
            } elseif (is_array($val)) {
                $val = implode(' ', $val);
            }

            $val = ($this->escapeHtmlAttr)($val);

            if ('id' === $key) {
                $val = $this->normalizeId($prefix, $val);
            }

            if (false !== mb_strpos($val, '"')) {
                $xhtml .= sprintf(' %s=\'%s\'', $key, $val);
            } else {
                $xhtml .= sprintf(' %s="%s"', $key, $val);
            }
        }

        return $xhtml;
    }

    /**
     * Normalize an ID
     *
     * @param string $prefix
     * @param string $value
     *
     * @return string
     */
    private function normalizeId(string $prefix, string $value): string
    {
        $prefix = mb_strtolower(trim(mb_substr($prefix, (int) mb_strrpos($prefix, '\\')), '\\'));

        return $prefix . '-' . $value;
    }
}
