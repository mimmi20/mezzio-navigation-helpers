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

use Laminas\View\Exception;
use Mezzio\Navigation;

interface ContainerParserInterface extends HelperInterface
{
    /**
     * Verifies container and eventually fetches it from service locator if it is a string
     *
     * @param int|Navigation\ContainerInterface|string|null $container
     *
     * @throws Exception\InvalidArgumentException
     */
    public function parseContainer($container = null): ?Navigation\ContainerInterface;
}
