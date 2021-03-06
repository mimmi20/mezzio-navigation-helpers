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

use Mezzio\GenericAuthorization\AuthorizationInterface;
use Mezzio\Navigation\Page\PageInterface;

final class AcceptHelper implements AcceptHelperInterface
{
    /**
     * Authorization to use when iterating pages
     */
    private ?AuthorizationInterface $authorization = null;

    /**
     * Whether invisible items should be rendered by this helper
     */
    private bool $renderInvisible = false;

    /**
     * Authorization role to use when iterating pages
     */
    private ?string $role = null;

    public function __construct(
        ?AuthorizationInterface $authorization,
        bool $renderInvisible,
        ?string $role
    ) {
        $this->authorization   = $authorization;
        $this->renderInvisible = $renderInvisible;
        $this->role            = $role;
    }

    /**
     * Determines whether a page should be accepted when iterating
     *
     * Rules:
     * - If a page is not visible it is not accepted, unless RenderInvisible has
     *   been set to true
     * - If $useAuthorization is true (default is true):
     *      - Page is accepted if Authorization returns true, otherwise false
     * - If page is accepted and $recursive is true, the page
     *   will not be accepted if it is the descendant of a non-accepted page
     *
     * @param PageInterface $page      page to check
     * @param bool          $recursive [optional] if true, page will not be
     *                                 accepted if it is the descendant of
     *                                 a page that is not accepted. Default
     *                                 is true
     *
     * @return bool Whether page should be accepted
     */
    public function accept(PageInterface $page, bool $recursive = true): bool
    {
        if (!$page->isVisible(false) && !$this->renderInvisible) {
            return false;
        }

        $accept    = true;
        $resource  = $page->getResource();
        $privilege = $page->getPrivilege();

        if (null !== $this->authorization && (null !== $resource || null !== $privilege)) {
            $accept = $this->authorization->isGranted($this->role, $resource, $privilege);
        }

        if ($accept && $recursive) {
            $parent = $page->getParent();

            if ($parent instanceof PageInterface) {
                $accept = $this->accept($parent, true);
            }
        }

        return $accept;
    }

    public function getAuthorization(): ?AuthorizationInterface
    {
        return $this->authorization;
    }

    public function getRenderInvisible(): bool
    {
        return $this->renderInvisible;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }
}
