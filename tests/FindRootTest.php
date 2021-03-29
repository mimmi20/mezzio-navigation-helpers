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

use Mezzio\Navigation\ContainerInterface;
use Mezzio\Navigation\Helper\FindRoot;
use Mezzio\Navigation\Page\PageInterface;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function assert;

final class FindRootTest extends TestCase
{
    private FindRoot $findRoot;

    protected function setUp(): void
    {
        $this->findRoot = new FindRoot();
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testSetRoot(): void
    {
        $root = $this->createMock(ContainerInterface::class);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::never())
            ->method('getParent');

        assert($root instanceof ContainerInterface);
        $this->findRoot->setRoot($root);

        assert($page instanceof PageInterface);
        self::assertSame($root, $this->findRoot->find($page));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testFindRootRecursive(): void
    {
        $root = $this->createMock(ContainerInterface::class);
        assert($root instanceof ContainerInterface);

        $parentPage = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parentPage->expects(self::once())
            ->method('getParent')
            ->willReturn($root);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('getParent')
            ->willReturn($parentPage);

        assert($page instanceof PageInterface);
        self::assertSame($root, $this->findRoot->find($page));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testFindRootWithoutParent(): void
    {
        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('getParent')
            ->willReturn(null);

        assert($page instanceof PageInterface);
        self::assertSame($page, $this->findRoot->find($page));
    }
}
