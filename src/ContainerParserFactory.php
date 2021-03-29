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

use Interop\Container\ContainerInterface;

final class ContainerParserFactory
{
    /**
     * Create and return a navigation view helper instance.
     */
    public function __invoke(ContainerInterface $container): ContainerParser
    {
        return new ContainerParser($container);
    }
}
