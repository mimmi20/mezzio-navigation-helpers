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

use Laminas\View\Exception\DomainException;
use Mezzio\Navigation\Page\PageInterface;

use function array_filter;
use function in_array;
use function sprintf;
use function ucfirst;

final class FindFromProperty implements FindFromPropertyInterface
{
    private AcceptHelperInterface $acceptHelper;

    private ConvertToPagesInterface $convertToPages;

    public function __construct(AcceptHelperInterface $acceptHelper, ConvertToPagesInterface $convertToPages)
    {
        $this->acceptHelper   = $acceptHelper;
        $this->convertToPages = $convertToPages;
    }

    /**
     * Finds relations of given $type for $page by checking if the
     * relation is specified as a property of $page
     *
     * @param PageInterface $page page to find relations for
     * @param string        $rel  relation, 'rel' or 'rev'
     * @param string        $type link type, e.g. 'start', 'next'
     *
     * @return array<PageInterface>
     *
     * @throws DomainException
     */
    public function find(PageInterface $page, string $rel, string $type): array
    {
        if (!in_array($rel, ['rel', 'rev'], true)) {
            throw new DomainException(
                sprintf(
                    'Invalid relation attribute "%s", must be "rel" or "rev"',
                    $rel
                )
            );
        }

        $method = 'get' . ucfirst($rel);
        $result = $page->{$method}($type);

        if (!$result) {
            return [];
        }

        $result = $this->convertToPages->convert($result);

        if ([] === $result) {
            return [];
        }

        return array_filter(
            $result,
            fn (PageInterface $page): bool => $this->acceptHelper->accept($page)
        );
    }
}
