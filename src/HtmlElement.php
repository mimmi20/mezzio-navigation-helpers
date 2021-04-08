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

use function array_filter;
use function implode;
use function is_array;
use function is_scalar;
use function is_string;
use function mb_strlen;
use function mb_strpos;
use function mb_strrpos;
use function mb_strtolower;
use function mb_substr;
use function sprintf;
use function trim;

final class HtmlElement implements HtmlElementInterface
{
    private EscapeHtml $escaper;

    private EscapeHtmlAttr $escapeHtmlAttr;

    public function __construct(EscapeHtml $escaper, EscapeHtmlAttr $escapeHtmlAttr)
    {
        $this->escaper        = $escaper;
        $this->escapeHtmlAttr = $escapeHtmlAttr;
    }

    /**
     * Returns an HTML string
     *
     * @param array<string, array<string>|bool|float|int|iterable|string|null> $attribs
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
     * @param array<string, array<string>|bool|float|int|iterable|string|null> $attribs an array where each key-value pair is converted
     *                                                                                  to an attribute name and value
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
     */
    private function normalizeId(string $prefix, string $value): string
    {
        $prefix = mb_strtolower(trim(mb_substr($prefix, (int) mb_strrpos($prefix, '\\')), '\\'));

        return $prefix . '-' . $value;
    }
}
