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
use Laminas\Log\Logger;
use Mezzio\Navigation\Helper\ConvertToPages;
use Mezzio\Navigation\Helper\ConvertToPagesFactory;
use Mezzio\Navigation\Page\PageFactoryInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function assert;

final class ConvertToPagesFactoryTest extends TestCase
{
    private ConvertToPagesFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ConvertToPagesFactory();
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testInvocation(): void
    {
        $logger      = $this->createMock(Logger::class);
        $pageFactory = $this->createMock(PageFactoryInterface::class);

        $container = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $container->expects(self::exactly(2))
            ->method('get')
            ->withConsecutive([Logger::class], [PageFactoryInterface::class])
            ->willReturnOnConsecutiveCalls($logger, $pageFactory);

        assert($container instanceof ContainerInterface);
        $helper = ($this->factory)($container);

        self::assertInstanceOf(ConvertToPages::class, $helper);
    }
}
