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
use Laminas\ServiceManager\PluginManagerInterface;
use Mezzio\GenericAuthorization\AuthorizationInterface;
use Mezzio\Navigation\Helper\AcceptHelperInterface;
use Mezzio\Navigation\Helper\FindActive;
use Mezzio\Navigation\Helper\FindActiveFactory;
use Mezzio\Navigation\Helper\PluginManager as HelperPluginManager;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function assert;

final class FindActiveFactoryTest extends TestCase
{
    private FindActiveFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new FindActiveFactory();
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testInvocationWithoutOptions(): void
    {
        $options = [
            'authorization' => null,
            'renderInvisible' => false,
            'role' => null,
        ];

        $acceptHelper = $this->getMockBuilder(AcceptHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $acceptHelper->expects(self::never())
            ->method('accept');

        $helperPluginManager = $this->getMockBuilder(PluginManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $helperPluginManager->expects(self::never())
            ->method('get');
        $helperPluginManager->expects(self::never())
            ->method('has');
        $helperPluginManager->expects(self::once())
            ->method('build')
            ->with(AcceptHelperInterface::class, $options)
            ->willReturn($acceptHelper);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('get')
            ->with(HelperPluginManager::class)
            ->willReturn($helperPluginManager);

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container, '');

        self::assertInstanceOf(FindActive::class, $helper);
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testInvocationWithOptions(): void
    {
        $auth            = $this->createMock(AuthorizationInterface::class);
        $renderInvisible = true;
        $role            = 'test-role';

        $options = [
            'authorization' => $auth,
            'renderInvisible' => $renderInvisible,
            'role' => $role,
        ];

        $acceptHelper = $this->getMockBuilder(AcceptHelperInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $acceptHelper->expects(self::never())
            ->method('accept');

        $helperPluginManager = $this->getMockBuilder(PluginManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $helperPluginManager->expects(self::never())
            ->method('get');
        $helperPluginManager->expects(self::never())
            ->method('has');
        $helperPluginManager->expects(self::once())
            ->method('build')
            ->with(AcceptHelperInterface::class, $options)
            ->willReturn($acceptHelper);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::once())
            ->method('get')
            ->with(HelperPluginManager::class)
            ->willReturn($helperPluginManager);

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)(
            $container,
            '',
            $options
        );

        self::assertInstanceOf(FindActive::class, $helper);
    }
}
