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

namespace MezzioTest\Navigation\Helper;

use Laminas\View\Helper\EscapeHtml;
use Laminas\View\Helper\EscapeHtmlAttr;
use Mezzio\Navigation\Helper\HtmlElement;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

final class HtmlElementTest extends TestCase
{
    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testToHtml(): void
    {
        $expected = '<a id="breadcrumbs-testIdEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped" targetEscaped="_blankEscaped" onClick=\'{"a":"b"}\' data-test="test-class1 test-class2">testLabelTranslatedAndEscaped</a>';

        $escapedTranslatedLabel = 'testLabelTranslatedAndEscaped';
        $id                     = 'testId';
        $class                  = 'test-class';
        $href                   = '#';
        $target                 = '_blank';
        $onclick                = (object) ['a' => 'b'];
        $testData               = ['test-class1', 'test-class2'];

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::exactly(6))
            ->method('__invoke')
            ->withConsecutive(
                ['id'],
                ['class'],
                ['href'],
                ['target'],
                ['onClick'],
                ['data-test']
            )
            ->willReturnOnConsecutiveCalls(
                'id',
                'classEscaped',
                'hrefEscaped',
                'targetEscaped',
                'onClick',
                'data-test'
            );

        $escapeHtmlAttr = $this->getMockBuilder(EscapeHtmlAttr::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtmlAttr->expects(self::exactly(6))
            ->method('__invoke')
            ->withConsecutive(
                [$id],
                [$class],
                [$href],
                [$target],
                ['{"a":"b"}'],
                ['test-class1 test-class2']
            )
            ->willReturnOnConsecutiveCalls(
                'testIdEscaped',
                'testClassEscaped',
                '#Escaped',
                '_blankEscaped',
                '{"a":"b"}',
                'test-class1 test-class2'
            );

        $element = 'a';

        $htmlElement = new HtmlElement($escapeHtml, $escapeHtmlAttr);

        self::assertSame(
            $expected,
            $htmlElement->toHtml(
                $element,
                ['id' => $id, 'title' => '', 'class' => $class, 'href' => $href, 'target' => $target, 'onClick' => $onclick, 'data-test' => $testData],
                $escapedTranslatedLabel,
                'Breadcrumbs'
            )
        );
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testToHtmlIgnoringNullAttributes(): void
    {
        $expected = '<a id="breadcrumbs-testIdEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped" targetEscaped="_blankEscaped" onClick=\'{"a":"b"}\' data-test="test-class1 test-class2">testLabelTranslatedAndEscaped</a>';

        $escapedTranslatedLabel = 'testLabelTranslatedAndEscaped';
        $id                     = 'testId';
        $class                  = 'test-class';
        $href                   = '#';
        $target                 = '_blank';
        $onclick                = (object) ['a' => 'b'];
        $testData               = ['test-class1', 'test-class2'];

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::exactly(6))
            ->method('__invoke')
            ->withConsecutive(
                ['id'],
                ['class'],
                ['href'],
                ['target'],
                ['onClick'],
                ['data-test']
            )
            ->willReturnOnConsecutiveCalls(
                'id',
                'classEscaped',
                'hrefEscaped',
                'targetEscaped',
                'onClick',
                'data-test'
            );

        $escapeHtmlAttr = $this->getMockBuilder(EscapeHtmlAttr::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtmlAttr->expects(self::exactly(6))
            ->method('__invoke')
            ->withConsecutive(
                [$id],
                [$class],
                [$href],
                [$target],
                ['{"a":"b"}'],
                ['test-class1 test-class2']
            )
            ->willReturnOnConsecutiveCalls(
                'testIdEscaped',
                'testClassEscaped',
                '#Escaped',
                '_blankEscaped',
                '{"a":"b"}',
                'test-class1 test-class2'
            );

        $element = 'a';

        $htmlElement = new HtmlElement($escapeHtml, $escapeHtmlAttr);

        self::assertSame(
            $expected,
            $htmlElement->toHtml(
                $element,
                ['id' => $id, 'title' => '', 'class' => $class, 'href' => $href, 'target' => $target, 'onClick' => $onclick, 'data-test' => $testData, 'open' => null],
                $escapedTranslatedLabel,
                'Breadcrumbs'
            )
        );
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testToHtmlNotIgnoringTrueAttributes(): void
    {
        $expected = '<a id="breadcrumbs-testIdEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped" targetEscaped="_blankEscaped" onClick=\'{"a":"b"}\' data-test="test-class1 test-class2" openEscaped>testLabelTranslatedAndEscaped</a>';

        $escapedTranslatedLabel = 'testLabelTranslatedAndEscaped';
        $id                     = 'testId';
        $class                  = 'test-class';
        $href                   = '#';
        $target                 = '_blank';
        $onclick                = (object) ['a' => 'b'];
        $testData               = ['test-class1', 'test-class2'];

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::exactly(7))
            ->method('__invoke')
            ->withConsecutive(
                ['id'],
                ['class'],
                ['href'],
                ['target'],
                ['onClick'],
                ['data-test'],
                ['open']
            )
            ->willReturnOnConsecutiveCalls(
                'id',
                'classEscaped',
                'hrefEscaped',
                'targetEscaped',
                'onClick',
                'data-test',
                'openEscaped'
            );

        $escapeHtmlAttr = $this->getMockBuilder(EscapeHtmlAttr::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtmlAttr->expects(self::exactly(6))
            ->method('__invoke')
            ->withConsecutive(
                [$id],
                [$class],
                [$href],
                [$target],
                ['{"a":"b"}'],
                ['test-class1 test-class2']
            )
            ->willReturnOnConsecutiveCalls(
                'testIdEscaped',
                'testClassEscaped',
                '#Escaped',
                '_blankEscaped',
                '{"a":"b"}',
                'test-class1 test-class2'
            );

        $element = 'a';

        $htmlElement = new HtmlElement($escapeHtml, $escapeHtmlAttr);

        self::assertSame(
            $expected,
            $htmlElement->toHtml(
                $element,
                ['id' => $id, 'title' => '', 'class' => $class, 'href' => $href, 'target' => $target, 'onClick' => $onclick, 'data-test' => $testData, 'open' => true],
                $escapedTranslatedLabel,
                'Breadcrumbs'
            )
        );
    }
}
