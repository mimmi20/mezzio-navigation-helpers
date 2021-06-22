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

use Mezzio\GenericAuthorization\AuthorizationInterface;
use Mezzio\Navigation\ContainerInterface;
use Mezzio\Navigation\Helper\AcceptHelper;
use Mezzio\Navigation\Page\PageInterface;
use Mezzio\Navigation\Page\Uri;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

use function assert;

final class AcceptHelperTest extends TestCase
{
    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testDoNotAcceptInvisiblePages(): void
    {
        $role = 'testRole';
        $auth = $this->createMock(AuthorizationInterface::class);

        assert($auth instanceof AuthorizationInterface);
        $helper = new AcceptHelper($auth, false, $role);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(false);
        $page->expects(self::never())
            ->method('getResource');
        $page->expects(self::never())
            ->method('getPrivilege');
        $page->expects(self::never())
            ->method('hashCode');
        $page->expects(self::never())
            ->method('getOrder');
        $page->expects(self::never())
            ->method('setParent');

        assert($page instanceof PageInterface);
        self::assertFalse($helper->accept($page));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testDoNotAcceptByAuthorization(): void
    {
        $role      = 'testRole';
        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::once())
            ->method('isGranted')
            ->with($role, $resource, $privilege)
            ->willReturn(false);

        assert($auth instanceof AuthorizationInterface);
        $helper = new AcceptHelper($auth, false, $role);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $page->expects(self::once())
            ->method('getResource')
            ->willReturn($resource);
        $page->expects(self::once())
            ->method('getPrivilege')
            ->willReturn($privilege);
        $page->expects(self::never())
            ->method('hashCode');
        $page->expects(self::never())
            ->method('getOrder');
        $page->expects(self::never())
            ->method('setParent');

        assert($page instanceof PageInterface);
        self::assertFalse($helper->accept($page));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testDoNotAcceptByAuthorizationWithParent(): void
    {
        $role      = 'testRole';
        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::once())
            ->method('isGranted')
            ->with($role, $resource, $privilege)
            ->willReturn(true);

        assert($auth instanceof AuthorizationInterface);
        $helper = new AcceptHelper($auth, false, $role);

        $parentPage = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parentPage->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(false);
        $parentPage->expects(self::never())
            ->method('hashCode');
        $parentPage->expects(self::never())
            ->method('getOrder');
        $parentPage->expects(self::never())
            ->method('setParent');

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $page->expects(self::once())
            ->method('getResource')
            ->willReturn($resource);
        $page->expects(self::once())
            ->method('getPrivilege')
            ->willReturn($privilege);
        $page->expects(self::once())
            ->method('getParent')
            ->willReturn($parentPage);
        $page->expects(self::never())
            ->method('hashCode');
        $page->expects(self::never())
            ->method('getOrder');
        $page->expects(self::never())
            ->method('setParent');

        assert($page instanceof PageInterface);
        self::assertFalse($helper->accept($page));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testDoNotAcceptByAuthorizationWithParent2(): void
    {
        $role = 'testRole';

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::never())
            ->method('isGranted');

        assert($auth instanceof AuthorizationInterface);
        $helper = new AcceptHelper($auth, false, $role);

        $parentPage = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parentPage->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(false);
        $parentPage->expects(self::never())
            ->method('hashCode');
        $parentPage->expects(self::never())
            ->method('getOrder');
        $parentPage->expects(self::never())
            ->method('setParent');

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $page->expects(self::once())
            ->method('getResource')
            ->willReturn(null);
        $page->expects(self::once())
            ->method('getPrivilege')
            ->willReturn(null);
        $page->expects(self::once())
            ->method('getParent')
            ->willReturn($parentPage);
        $page->expects(self::never())
            ->method('hashCode');
        $page->expects(self::never())
            ->method('getOrder');
        $page->expects(self::never())
            ->method('setParent');

        assert($page instanceof PageInterface);
        self::assertFalse($helper->accept($page));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testDoNotAcceptByAuthorizationWithParent3(): void
    {
        $role      = 'testRole';
        $privilege = 'testPrivilege';

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::once())
            ->method('isGranted')
            ->with($role, null, $privilege)
            ->willReturn(true);

        assert($auth instanceof AuthorizationInterface);
        $helper = new AcceptHelper($auth, false, $role);

        $parentPage = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parentPage->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(false);
        $parentPage->expects(self::never())
            ->method('hashCode');
        $parentPage->expects(self::never())
            ->method('getOrder');
        $parentPage->expects(self::never())
            ->method('setParent');

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $page->expects(self::once())
            ->method('getResource')
            ->willReturn(null);
        $page->expects(self::once())
            ->method('getPrivilege')
            ->willReturn($privilege);
        $page->expects(self::once())
            ->method('getParent')
            ->willReturn($parentPage);
        $page->expects(self::never())
            ->method('hashCode');
        $page->expects(self::never())
            ->method('getOrder');
        $page->expects(self::never())
            ->method('setParent');

        assert($page instanceof PageInterface);
        self::assertFalse($helper->accept($page));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testDoNotAcceptByAuthorizationWithParent4(): void
    {
        $role     = 'testRole';
        $resource = 'testResource';

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::once())
            ->method('isGranted')
            ->with($role, $resource, null)
            ->willReturn(true);

        assert($auth instanceof AuthorizationInterface);
        $helper = new AcceptHelper($auth, false, $role);

        $parentPage = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parentPage->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(false);
        $parentPage->expects(self::never())
            ->method('hashCode');
        $parentPage->expects(self::never())
            ->method('getOrder');
        $parentPage->expects(self::never())
            ->method('setParent');

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $page->expects(self::once())
            ->method('getResource')
            ->willReturn($resource);
        $page->expects(self::once())
            ->method('getPrivilege')
            ->willReturn(null);
        $page->expects(self::once())
            ->method('getParent')
            ->willReturn($parentPage);
        $page->expects(self::never())
            ->method('hashCode');
        $page->expects(self::never())
            ->method('getOrder');
        $page->expects(self::never())
            ->method('setParent');

        assert($page instanceof PageInterface);
        self::assertFalse($helper->accept($page));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testDoNotAcceptByAuthorizationWithParent5(): void
    {
        $role     = 'testRole';
        $resource = 'testResource';

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::once())
            ->method('isGranted')
            ->with($role, $resource, null)
            ->willReturn(true);

        assert($auth instanceof AuthorizationInterface);
        $helper = new AcceptHelper($auth, false, $role);

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $page->expects(self::once())
            ->method('getResource')
            ->willReturn($resource);
        $page->expects(self::once())
            ->method('getPrivilege')
            ->willReturn(null);
        $page->expects(self::never())
            ->method('getParent');
        $page->expects(self::never())
            ->method('hashCode');
        $page->expects(self::never())
            ->method('getOrder');
        $page->expects(self::never())
            ->method('setParent');

        assert($page instanceof PageInterface);
        self::assertTrue($helper->accept($page, false));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testDoNotAcceptByAuthorizationWithParent6(): void
    {
        $role      = 'testRole';
        $resource  = 'testResource';
        $privilege = 'testPrivilege';

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::once())
            ->method('isGranted')
            ->with($role, $resource, $privilege)
            ->willReturn(true);

        assert($auth instanceof AuthorizationInterface);
        $helper = new AcceptHelper($auth, false, $role);

        $parentPage = $this->getMockBuilder(ContainerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $page = $this->getMockBuilder(PageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $page->expects(self::once())
            ->method('isVisible')
            ->with(false)
            ->willReturn(true);
        $page->expects(self::once())
            ->method('getResource')
            ->willReturn($resource);
        $page->expects(self::once())
            ->method('getPrivilege')
            ->willReturn($privilege);
        $page->expects(self::once())
            ->method('getParent')
            ->willReturn($parentPage);
        $page->expects(self::never())
            ->method('hashCode');
        $page->expects(self::never())
            ->method('getOrder');
        $page->expects(self::never())
            ->method('setParent');

        assert($page instanceof PageInterface);
        self::assertTrue($helper->accept($page));
    }

    /**
     * @throws Exception
     * @throws InvalidArgumentException
     * @throws \Mezzio\Navigation\Exception\InvalidArgumentException
     */
    public function testDoNotAcceptInvisibleParent(): void
    {
        $parentPage = new Uri();
        $parentPage->setUri('page2/page2_3/page2_3_2');
        $parentPage->setVisible('0');

        $page = new Uri();
        $page->setUri('page2/page2_3/page2_3_2/1');
        $page->setActive('1');

        $parentPage->addPage($page);

        $role = 'testRole';

        $auth = $this->getMockBuilder(AuthorizationInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $auth->expects(self::never())
            ->method('isGranted');

        assert($auth instanceof AuthorizationInterface);
        $helper = new AcceptHelper($auth, false, $role);

        assert($page instanceof PageInterface);
        self::assertFalse($helper->accept($page));
    }
}
