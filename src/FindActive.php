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

use Mezzio\Navigation\ContainerInterface;
use Mezzio\Navigation\Page\PageInterface;
use RecursiveIteratorIterator;

use function assert;
use function get_class;
use function is_int;
use function sprintf;

final class FindActive implements FindActiveInterface
{
    private const START_DEPTH = -1;

    private AcceptHelperInterface $acceptHelper;

    public function __construct(AcceptHelperInterface $acceptHelper)
    {
        $this->acceptHelper = $acceptHelper;
    }

    /**
     * Finds the deepest active page in the given container
     *
     * @param ContainerInterface $container to search
     * @param int|null           $minDepth  [optional] minimum depth
     *                                      required for page to be
     *                                      valid. Default is to use
     *                                      {@link getMinDepth()}. A
     *                                      null value means no minimum
     *                                      depth required.
     * @param int|null           $maxDepth  [optional] maximum depth
     *                                      a page can have to be
     *                                      valid. Default is to use
     *                                      {@link getMaxDepth()}. A
     *                                      null value means no maximum
     *                                      depth required.
     *
     * @return array<string, int|PageInterface|null> an associative array with the values 'depth' and 'page', or an empty array if not found
     * @phpstan-return array{page?: PageInterface|null, depth?: int|null}
     */
    public function find(ContainerInterface $container, ?int $minDepth, ?int $maxDepth): array
    {
        $found      = null;
        $foundDepth = self::START_DEPTH;
        $iterator   = new RecursiveIteratorIterator(
            $container,
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $page) {
            assert(
                $page instanceof PageInterface,
                sprintf(
                    '$page should be an Instance of %s, but was %s',
                    PageInterface::class,
                    get_class($page)
                )
            );

            $currDepth = $iterator->getDepth();

            if ($currDepth < $minDepth || !$this->acceptHelper->accept($page)) {
                // page is not accepted
                continue;
            }

            if ($currDepth <= $foundDepth || !$page->isActive(false)) {
                continue;
            }

            // found an active page at a deeper level than before
            $found      = $page;
            $foundDepth = $currDepth;
        }

        if (is_int($maxDepth) && $foundDepth > $maxDepth && $found instanceof PageInterface) {
            while ($foundDepth > $maxDepth) {
                assert($foundDepth >= $minDepth);

                if (--$foundDepth < $minDepth) {
                    $found = null;
                    break;
                }

                $found = $found->getParent();
                if (!$found instanceof PageInterface) {
                    $found = null;
                    break;
                }
            }
        }

        if ($found instanceof PageInterface) {
            return ['page' => $found, 'depth' => $foundDepth];
        }

        return [];
    }
}
