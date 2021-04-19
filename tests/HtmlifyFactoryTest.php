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

use Interop\Container\ContainerInterface;
use Laminas\I18n\View\Helper\Translate;
use Laminas\ServiceManager\PluginManagerInterface;
use Laminas\View\Helper\EscapeHtml;
use Laminas\View\HelperPluginManager;
use Mezzio\LaminasViewHelper\Helper\HtmlElementInterface;
use Mezzio\LaminasViewHelper\Helper\PluginManager;
use Mezzio\Navigation\Helper\Htmlify;
use Mezzio\Navigation\Helper\HtmlifyFactory;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function assert;

final class HtmlifyFactoryTest extends TestCase
{
    private HtmlifyFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new HtmlifyFactory();
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testInvocationWithoutTranslator(): void
    {
        $escapeHtml  = $this->createMock(EscapeHtml::class);
        $htmlElement = $this->createMock(HtmlElementInterface::class);

        $pluginManager = $this->getMockBuilder(PluginManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pluginManager->expects(self::once())
            ->method('get')
            ->with(HtmlElementInterface::class)
            ->willReturn($htmlElement);

        $helperPluginManager = $this->getMockBuilder(HelperPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $helperPluginManager->expects(self::once())
            ->method('get')
            ->with(EscapeHtml::class)
            ->willReturn($escapeHtml);
        $helperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(false);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive([PluginManager::class], [HelperPluginManager::class])
            ->willReturnOnConsecutiveCalls($pluginManager, $helperPluginManager);

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(Htmlify::class, $helper);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testInvocationWithTranslator(): void
    {
        $escapeHtml      = $this->createMock(EscapeHtml::class);
        $htmlElement     = $this->createMock(HtmlElementInterface::class);
        $translatePlugin = $this->createMock(Translate::class);

        $pluginManager = $this->getMockBuilder(PluginManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $pluginManager->expects(self::once())
            ->method('get')
            ->with(HtmlElementInterface::class)
            ->willReturn($htmlElement);

        $helperPluginManager = $this->getMockBuilder(HelperPluginManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $helperPluginManager->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive([Translate::class], [EscapeHtml::class])
            ->willReturnOnConsecutiveCalls($translatePlugin, $escapeHtml);
        $helperPluginManager->expects(self::once())
            ->method('has')
            ->with(Translate::class)
            ->willReturn(true);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive([PluginManager::class], [HelperPluginManager::class])
            ->willReturnOnConsecutiveCalls($pluginManager, $helperPluginManager);

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(Htmlify::class, $helper);
    }
}
