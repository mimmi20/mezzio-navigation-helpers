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

use Laminas\I18n\View\Helper\Translate;
use Laminas\View\Helper\EscapeHtml;
use Mezzio\Navigation\Helper\HtmlElementInterface;
use Mezzio\Navigation\Helper\Htmlify;
use Mezzio\Navigation\Page\PageInterface;
use PHPUnit\Framework\TestCase;

final class HtmlifyTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @return void
     */
    public function testHtmlify(): void
    {
        $expected = '<a id="breadcrumbs-testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped" targetEscaped="_blankEscaped">testLabelTranslatedAndEscaped</a>';

        $label                  = 'testLabel';
        $translatedLabel        = 'testLabelTranslated';
        $escapedTranslatedLabel = 'testLabelTranslatedAndEscaped';
        $title                  = 'testTitle';
        $tranalatedTitle        = 'testTitleTranslated';
        $textDomain             = 'testDomain';
        $id                     = 'testId';
        $class                  = 'test-class';
        $href                   = '#';
        $target                 = '_blank';

        $translatePlugin = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $translatePlugin->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive([$label, $textDomain], [$title, $textDomain])
            ->willReturnOnConsecutiveCalls($translatedLabel, $tranalatedTitle);

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($translatedLabel)
            ->willReturn($escapedTranslatedLabel);

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('a', ['id' => $id, 'title' => $tranalatedTitle, 'class' => $class, 'href' => $href, 'target' => $target], $escapedTranslatedLabel, 'Breadcrumbs')
            ->willReturn($expected);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $page->expects(self::once())
            ->method('getTitle')
            ->willReturn($title);
        $page->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($textDomain);
        $page->expects(self::once())
            ->method('getId')
            ->willReturn($id);
        $page->expects(self::once())
            ->method('getClass')
            ->willReturn($class);
        $page->expects(self::once())
            ->method('getHref')
            ->willReturn($href);
        $page->expects(self::once())
            ->method('getTarget')
            ->willReturn($target);
        $page->expects(self::once())
            ->method('getCustomProperties')
            ->willReturn([]);

        \assert($escapeHtml instanceof EscapeHtml);
        \assert($htmlElement instanceof HtmlElementInterface);
        \assert($translatePlugin instanceof Translate);
        $helper = new Htmlify($escapeHtml, $htmlElement, $translatePlugin);

        /* @var PageInterface $page */
        self::assertSame($expected, $helper->toHtml('Breadcrumbs', $page));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @return void
     */
    public function testHtmlifyWithoutTranslator(): void
    {
        $expected = '<a id="breadcrumbs-testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabelEscaped</a>';

        $label        = 'testLabel';
        $escapedLabel = 'testLabelEscaped';
        $title        = 'testTitle';
        $id           = 'testId';
        $class        = 'test-class';
        $href         = '#';
        $target       = null;

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($label)
            ->willReturn($escapedLabel);

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('a', ['id' => $id, 'title' => $title, 'class' => $class, 'href' => $href, 'target' => $target], $escapedLabel, 'Breadcrumbs')
            ->willReturn($expected);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $page->expects(self::once())
            ->method('getTitle')
            ->willReturn($title);
        $page->expects(self::never())
            ->method('getTextDomain');
        $page->expects(self::once())
            ->method('getId')
            ->willReturn($id);
        $page->expects(self::once())
            ->method('getClass')
            ->willReturn($class);
        $page->expects(self::once())
            ->method('getHref')
            ->willReturn($href);
        $page->expects(self::once())
            ->method('getTarget')
            ->willReturn($target);
        $page->expects(self::once())
            ->method('getCustomProperties')
            ->willReturn([]);

        \assert($escapeHtml instanceof EscapeHtml);
        \assert($htmlElement instanceof HtmlElementInterface);
        $helper = new Htmlify($escapeHtml, $htmlElement);

        /* @var PageInterface $page */
        self::assertSame($expected, $helper->toHtml('Breadcrumbs', $page));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @return void
     */
    public function testHtmlifyWithoutEscapingLabel(): void
    {
        $expected = '<a id="breadcrumbs-testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped" targetEscaped="_blankEscaped">testLabelTranslated</a>';

        $label           = 'testLabel';
        $translatedLabel = 'testLabelTranslated';
        $title           = 'testTitle';
        $tranalatedTitle = 'testTitleTranslated';
        $textDomain      = 'testDomain';
        $id              = 'testId';
        $class           = 'test-class';
        $href            = '#';
        $target          = '_blank';

        $translatePlugin = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $translatePlugin->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive([$label, $textDomain], [$title, $textDomain])
            ->willReturnOnConsecutiveCalls($translatedLabel, $tranalatedTitle);

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('a', ['id' => $id, 'title' => $tranalatedTitle, 'class' => $class, 'href' => $href, 'target' => $target], $translatedLabel, 'Breadcrumbs')
            ->willReturn($expected);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $page->expects(self::once())
            ->method('getTitle')
            ->willReturn($title);
        $page->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($textDomain);
        $page->expects(self::once())
            ->method('getId')
            ->willReturn($id);
        $page->expects(self::once())
            ->method('getClass')
            ->willReturn($class);
        $page->expects(self::once())
            ->method('getHref')
            ->willReturn($href);
        $page->expects(self::once())
            ->method('getTarget')
            ->willReturn($target);
        $page->expects(self::once())
            ->method('getCustomProperties')
            ->willReturn([]);

        \assert($escapeHtml instanceof EscapeHtml);
        \assert($htmlElement instanceof HtmlElementInterface);
        \assert($translatePlugin instanceof Translate);
        $helper = new Htmlify($escapeHtml, $htmlElement, $translatePlugin);

        /* @var PageInterface $page */
        self::assertSame($expected, $helper->toHtml('Breadcrumbs', $page, false));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @return void
     */
    public function testHtmlifyWithoutTranslatorAndEscapingLabel(): void
    {
        $expected = '<a id="breadcrumbs-testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped">testLabel</a>';

        $label  = 'testLabel';
        $title  = 'testTitle';
        $id     = 'testId';
        $class  = 'test-class';
        $href   = '#';
        $target = null;

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::never())
            ->method('__invoke');

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('a', ['id' => $id, 'title' => $title, 'class' => $class, 'href' => $href, 'target' => $target], $label, 'Breadcrumbs')
            ->willReturn($expected);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $page->expects(self::once())
            ->method('getTitle')
            ->willReturn($title);
        $page->expects(self::never())
            ->method('getTextDomain');
        $page->expects(self::once())
            ->method('getId')
            ->willReturn($id);
        $page->expects(self::once())
            ->method('getClass')
            ->willReturn($class);
        $page->expects(self::once())
            ->method('getHref')
            ->willReturn($href);
        $page->expects(self::once())
            ->method('getTarget')
            ->willReturn($target);
        $page->expects(self::once())
            ->method('getCustomProperties')
            ->willReturn([]);

        \assert($escapeHtml instanceof EscapeHtml);
        \assert($htmlElement instanceof HtmlElementInterface);
        $helper = new Htmlify($escapeHtml, $htmlElement);

        /* @var PageInterface $page */
        self::assertSame($expected, $helper->toHtml('Breadcrumbs', $page, false));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @return void
     */
    public function testHtmlifyWithClassOnListItem(): void
    {
        $expected = '<a id="breadcrumbs-testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" hrefEscaped="#Escaped" targetEscaped="_blankEscaped">testLabelTranslatedAndEscaped</a>';

        $label                  = 'testLabel';
        $translatedLabel        = 'testLabelTranslated';
        $escapedTranslatedLabel = 'testLabelTranslatedAndEscaped';
        $title                  = 'testTitle';
        $tranalatedTitle        = 'testTitleTranslated';
        $textDomain             = 'testDomain';
        $id                     = 'testId';
        $href                   = '#';
        $target                 = '_blank';

        $translatePlugin = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $translatePlugin->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive([$label, $textDomain], [$title, $textDomain])
            ->willReturnOnConsecutiveCalls($translatedLabel, $tranalatedTitle);

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($translatedLabel)
            ->willReturn($escapedTranslatedLabel);

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('a', ['id' => $id, 'title' => $tranalatedTitle, 'href' => $href, 'target' => $target], $escapedTranslatedLabel, 'Breadcrumbs')
            ->willReturn($expected);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $page->expects(self::once())
            ->method('getTitle')
            ->willReturn($title);
        $page->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($textDomain);
        $page->expects(self::once())
            ->method('getId')
            ->willReturn($id);
        $page->expects(self::never())
            ->method('getClass');
        $page->expects(self::once())
            ->method('getHref')
            ->willReturn($href);
        $page->expects(self::once())
            ->method('getTarget')
            ->willReturn($target);
        $page->expects(self::once())
            ->method('getCustomProperties')
            ->willReturn([]);

        \assert($escapeHtml instanceof EscapeHtml);
        \assert($htmlElement instanceof HtmlElementInterface);
        \assert($translatePlugin instanceof Translate);
        $helper = new Htmlify($escapeHtml, $htmlElement, $translatePlugin);

        /* @var PageInterface $page */
        self::assertSame($expected, $helper->toHtml('Breadcrumbs', $page, true, true));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @return void
     */
    public function testHtmlifyWithoutTranslatorAndWithClassOnListItem(): void
    {
        $expected = '<a id="breadcrumbs-testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" hrefEscaped="#Escaped">testLabelEscaped</a>';

        $label        = 'testLabel';
        $escapedLabel = 'testLabelEscaped';
        $title        = 'testTitle';
        $id           = 'testId';
        $href         = '#';
        $target       = null;

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($label)
            ->willReturn($escapedLabel);

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('a', ['id' => $id, 'title' => $title, 'href' => $href, 'target' => $target], $escapedLabel, 'Breadcrumbs')
            ->willReturn($expected);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $page->expects(self::once())
            ->method('getTitle')
            ->willReturn($title);
        $page->expects(self::never())
            ->method('getTextDomain');
        $page->expects(self::once())
            ->method('getId')
            ->willReturn($id);
        $page->expects(self::never())
            ->method('getClass');
        $page->expects(self::once())
            ->method('getHref')
            ->willReturn($href);
        $page->expects(self::once())
            ->method('getTarget')
            ->willReturn($target);
        $page->expects(self::once())
            ->method('getCustomProperties')
            ->willReturn([]);

        \assert($escapeHtml instanceof EscapeHtml);
        \assert($htmlElement instanceof HtmlElementInterface);
        $helper = new Htmlify($escapeHtml, $htmlElement);

        /* @var PageInterface $page */
        self::assertSame($expected, $helper->toHtml('Breadcrumbs', $page, true, true));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @return void
     */
    public function testHtmlifyWithoutHref(): void
    {
        $expected = '<span id="breadcrumbs-testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped">testLabelTranslatedAndEscaped</span>';

        $label                  = 'testLabel';
        $translatedLabel        = 'testLabelTranslated';
        $escapedTranslatedLabel = 'testLabelTranslatedAndEscaped';
        $title                  = 'testTitle';
        $tranalatedTitle        = 'testTitleTranslated';
        $textDomain             = 'testDomain';
        $id                     = 'testId';
        $class                  = 'test-class';

        $translatePlugin = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $translatePlugin->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive([$label, $textDomain], [$title, $textDomain])
            ->willReturnOnConsecutiveCalls($translatedLabel, $tranalatedTitle);

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($translatedLabel)
            ->willReturn($escapedTranslatedLabel);

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('span', ['id' => $id, 'title' => $tranalatedTitle, 'class' => $class], $escapedTranslatedLabel, 'Breadcrumbs')
            ->willReturn($expected);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $page->expects(self::once())
            ->method('getTitle')
            ->willReturn($title);
        $page->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($textDomain);
        $page->expects(self::once())
            ->method('getId')
            ->willReturn($id);
        $page->expects(self::once())
            ->method('getClass')
            ->willReturn($class);
        $page->expects(self::once())
            ->method('getHref')
            ->willReturn('');
        $page->expects(self::never())
            ->method('getTarget');
        $page->expects(self::once())
            ->method('getCustomProperties')
            ->willReturn([]);

        \assert($escapeHtml instanceof EscapeHtml);
        \assert($htmlElement instanceof HtmlElementInterface);
        \assert($translatePlugin instanceof Translate);
        $helper = new Htmlify($escapeHtml, $htmlElement, $translatePlugin);

        /* @var PageInterface $page */
        self::assertSame($expected, $helper->toHtml('Breadcrumbs', $page));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @return void
     */
    public function testHtmlifyWithoutTranslatorAndHref(): void
    {
        $expected = '<span id="breadcrumbs-testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped">testLabelEscaped</span>';

        $label        = 'testLabel';
        $escapedLabel = 'testLabelEscaped';
        $title        = 'testTitle';
        $id           = 'testId';
        $class        = 'test-class';

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($label)
            ->willReturn($escapedLabel);

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('span', ['id' => $id, 'title' => $title, 'class' => $class], $escapedLabel, 'Breadcrumbs')
            ->willReturn($expected);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $page->expects(self::once())
            ->method('getTitle')
            ->willReturn($title);
        $page->expects(self::never())
            ->method('getTextDomain');
        $page->expects(self::once())
            ->method('getId')
            ->willReturn($id);
        $page->expects(self::once())
            ->method('getClass')
            ->willReturn($class);
        $page->expects(self::once())
            ->method('getHref')
            ->willReturn('');
        $page->expects(self::never())
            ->method('getTarget');
        $page->expects(self::once())
            ->method('getCustomProperties')
            ->willReturn([]);

        \assert($escapeHtml instanceof EscapeHtml);
        \assert($htmlElement instanceof HtmlElementInterface);
        $helper = new Htmlify($escapeHtml, $htmlElement);

        /* @var PageInterface $page */
        self::assertSame($expected, $helper->toHtml('Breadcrumbs', $page));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @return void
     */
    public function testHtmlifyWithArrayOfClasses(): void
    {
        $expected = '<a id="breadcrumbs-testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped" targetEscaped="_blankEscaped" onClick=\'{"a":"b"}\' data-test="test-class1 test-class2">testLabelTranslatedAndEscaped</a>';

        $label                  = 'testLabel';
        $translatedLabel        = 'testLabelTranslated';
        $escapedTranslatedLabel = 'testLabelTranslatedAndEscaped';
        $title                  = 'testTitle';
        $tranalatedTitle        = 'testTitleTranslated';
        $textDomain             = 'testDomain';
        $id                     = 'testId';
        $class                  = 'test-class';
        $href                   = '#';
        $target                 = '_blank';
        $onclick                = (object) ['a' => 'b'];
        $testData               = ['test-class1', 'test-class2'];

        $translatePlugin = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $translatePlugin->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive([$label, $textDomain], [$title, $textDomain])
            ->willReturnOnConsecutiveCalls($translatedLabel, $tranalatedTitle);

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($translatedLabel)
            ->willReturn($escapedTranslatedLabel);

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('a', ['id' => $id, 'title' => $tranalatedTitle, 'class' => $class, 'href' => $href, 'target' => $target, 'onClick' => $onclick, 'data-test' => $testData], $escapedTranslatedLabel, 'Breadcrumbs')
            ->willReturn($expected);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::once())
            ->method('getLabel')
            ->willReturn($label);
        $page->expects(self::once())
            ->method('getTitle')
            ->willReturn($title);
        $page->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($textDomain);
        $page->expects(self::once())
            ->method('getId')
            ->willReturn($id);
        $page->expects(self::once())
            ->method('getClass')
            ->willReturn($class);
        $page->expects(self::once())
            ->method('getHref')
            ->willReturn($href);
        $page->expects(self::once())
            ->method('getTarget')
            ->willReturn($target);
        $page->expects(self::once())
            ->method('getCustomProperties')
            ->willReturn(['onClick' => $onclick, 'data-test' => $testData]);

        \assert($escapeHtml instanceof EscapeHtml);
        \assert($htmlElement instanceof HtmlElementInterface);
        \assert($translatePlugin instanceof Translate);
        $helper = new Htmlify($escapeHtml, $htmlElement, $translatePlugin);

        /* @var PageInterface $page */
        self::assertSame($expected, $helper->toHtml('Breadcrumbs', $page));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @return void
     */
    public function testHtmlifyWithArrayOfClasses2(): void
    {
        $expected = '<a id="breadcrumbs-testIdEscaped" titleEscaped="testTitleTranslatedAndEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped" targetEscaped="_blankEscaped" onClick=\'{"a":"b"}\' data-test="test-class1 test-class2">testLabelTranslatedAndEscaped</a>';

        $translatedLabel        = 'testLabelTranslated';
        $escapedTranslatedLabel = 'testLabelTranslatedAndEscaped';
        $tranalatedTitle        = 'testTitleTranslated';
        $textDomain             = 'testDomain';
        $id                     = 'testId';
        $class                  = 'test-class';
        $href                   = '#';
        $target                 = '_blank';
        $onclick                = (object) ['a' => 'b'];
        $testData               = ['test-class1', 'test-class2'];

        $translatePlugin = $this->getMockBuilder(Translate::class)
            ->disableOriginalConstructor()
            ->getMock();
        $translatePlugin->expects(self::exactly(2))
            ->method('__invoke')
            ->withConsecutive(['', $textDomain], ['', $textDomain])
            ->willReturnOnConsecutiveCalls($translatedLabel, $tranalatedTitle);

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with($translatedLabel)
            ->willReturn($escapedTranslatedLabel);

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('a', ['id' => $id, 'title' => $tranalatedTitle, 'class' => $class, 'href' => $href, 'target' => $target, 'onClick' => $onclick, 'data-test' => $testData], $escapedTranslatedLabel, 'Breadcrumbs')
            ->willReturn($expected);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::once())
            ->method('getLabel')
            ->willReturn(null);
        $page->expects(self::once())
            ->method('getTitle')
            ->willReturn(null);
        $page->expects(self::exactly(2))
            ->method('getTextDomain')
            ->willReturn($textDomain);
        $page->expects(self::once())
            ->method('getId')
            ->willReturn($id);
        $page->expects(self::once())
            ->method('getClass')
            ->willReturn($class);
        $page->expects(self::once())
            ->method('getHref')
            ->willReturn($href);
        $page->expects(self::once())
            ->method('getTarget')
            ->willReturn($target);
        $page->expects(self::once())
            ->method('getCustomProperties')
            ->willReturn(['onClick' => $onclick, 'data-test' => $testData]);

        \assert($escapeHtml instanceof EscapeHtml);
        \assert($htmlElement instanceof HtmlElementInterface);
        \assert($translatePlugin instanceof Translate);
        $helper = new Htmlify($escapeHtml, $htmlElement, $translatePlugin);

        /* @var PageInterface $page */
        self::assertSame($expected, $helper->toHtml('Breadcrumbs', $page));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @return void
     */
    public function testHtmlifyWithArrayOfClasses3(): void
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
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with('')
            ->willReturn($escapedTranslatedLabel);

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('a', ['id' => $id, 'title' => '', 'class' => $class, 'href' => $href, 'target' => $target, 'onClick' => $onclick, 'data-test' => $testData], $escapedTranslatedLabel, 'Breadcrumbs')
            ->willReturn($expected);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::once())
            ->method('getLabel')
            ->willReturn(null);
        $page->expects(self::once())
            ->method('getTitle')
            ->willReturn(null);
        $page->expects(self::never())
            ->method('getTextDomain');
        $page->expects(self::once())
            ->method('getId')
            ->willReturn($id);
        $page->expects(self::once())
            ->method('getClass')
            ->willReturn($class);
        $page->expects(self::once())
            ->method('getHref')
            ->willReturn($href);
        $page->expects(self::once())
            ->method('getTarget')
            ->willReturn($target);
        $page->expects(self::once())
            ->method('getCustomProperties')
            ->willReturn(['onClick' => $onclick, 'data-test' => $testData]);

        \assert($escapeHtml instanceof EscapeHtml);
        \assert($htmlElement instanceof HtmlElementInterface);
        $helper = new Htmlify($escapeHtml, $htmlElement);

        /* @var PageInterface $page */
        self::assertSame($expected, $helper->toHtml('Breadcrumbs', $page));
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     *
     * @return void
     */
    public function testHtmlifyWithArrayOfClassesAndAttributes(): void
    {
        $expected = '<a id="breadcrumbs-testIdEscaped" classEscaped="testClassEscaped" hrefEscaped="#Escaped" targetEscaped="_blankEscaped" onClick=\'{"a":"b"}\' data-test="test-class1 test-class2">testLabelTranslatedAndEscaped</a>';

        $escapedTranslatedLabel = 'testLabelTranslatedAndEscaped';
        $id                     = 'testId';
        $class                  = 'test-class';
        $href                   = '#';
        $target                 = '_blank';
        $onclick                = (object) ['a' => 'b'];
        $testData               = ['test-class1', 'test-class2'];
        $attributes             = ['data-bs-toggle' => 'dropdown', 'role' => 'button', 'aria-expanded' => 'false'];

        $escapeHtml = $this->getMockBuilder(EscapeHtml::class)
            ->disableOriginalConstructor()
            ->getMock();
        $escapeHtml->expects(self::once())
            ->method('__invoke')
            ->with('')
            ->willReturn($escapedTranslatedLabel);

        $htmlElement = $this->getMockBuilder(HtmlElementInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $htmlElement->expects(self::once())
            ->method('toHtml')
            ->with('a', ['id' => $id, 'title' => '', 'class' => $class, 'href' => $href, 'target' => $target, 'onClick' => $onclick, 'data-test' => $testData] + $attributes, $escapedTranslatedLabel, 'Breadcrumbs')
            ->willReturn($expected);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('isVisible');
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::once())
            ->method('getLabel')
            ->willReturn(null);
        $page->expects(self::once())
            ->method('getTitle')
            ->willReturn(null);
        $page->expects(self::never())
            ->method('getTextDomain');
        $page->expects(self::once())
            ->method('getId')
            ->willReturn($id);
        $page->expects(self::once())
            ->method('getClass')
            ->willReturn($class);
        $page->expects(self::once())
            ->method('getHref')
            ->willReturn($href);
        $page->expects(self::once())
            ->method('getTarget')
            ->willReturn($target);
        $page->expects(self::once())
            ->method('getCustomProperties')
            ->willReturn(['onClick' => $onclick, 'data-test' => $testData]);

        \assert($escapeHtml instanceof EscapeHtml);
        \assert($htmlElement instanceof HtmlElementInterface);
        $helper = new Htmlify($escapeHtml, $htmlElement);

        /* @var PageInterface $page */
        self::assertSame($expected, $helper->toHtml('Breadcrumbs', $page, true, false, $attributes));
    }
}
