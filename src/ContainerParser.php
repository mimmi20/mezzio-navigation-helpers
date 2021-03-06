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
use Laminas\View\Exception;
use Mezzio\Navigation;
use Psr\Container\ContainerExceptionInterface;

use function in_array;
use function is_string;
use function sprintf;

final class ContainerParser implements ContainerParserInterface
{
    private ContainerInterface $serviceLocator;

    public function __construct(ContainerInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Verifies container and eventually fetches it from service locator if it is a string
     *
     * @param int|Navigation\ContainerInterface|string|null $container
     *
     * @throws Exception\InvalidArgumentException
     */
    public function parseContainer($container = null): ?Navigation\ContainerInterface
    {
        if (null === $container || $container instanceof Navigation\ContainerInterface) {
            return $container;
        }

        if (is_string($container)) {
            // Fallback
            if (in_array($container, ['default', 'navigation'], true)) {
                // Uses class name
                if ($this->serviceLocator->has(Navigation\Navigation::class)) {
                    try {
                        $container = $this->serviceLocator->get(Navigation\Navigation::class);
                    } catch (ContainerExceptionInterface $e) {
                        throw new Exception\InvalidArgumentException(
                            sprintf('Could not load Container "%s"', Navigation\Navigation::class),
                            0,
                            $e
                        );
                    }

                    return $container;
                }

                // Uses old service name
                if ($this->serviceLocator->has('navigation')) {
                    try {
                        $container = $this->serviceLocator->get('navigation');
                    } catch (ContainerExceptionInterface $e) {
                        throw new Exception\InvalidArgumentException(
                            'Could not load Container "navigation"',
                            0,
                            $e
                        );
                    }

                    return $container;
                }
            }

            /*
             * Load the navigation container from the root service locator
             */
            try {
                $container = $this->serviceLocator->get($container);
            } catch (ContainerExceptionInterface $e) {
                throw new Exception\InvalidArgumentException(
                    sprintf('Could not load Container "%s"', $container),
                    0,
                    $e
                );
            }

            return $container;
        }

        throw new Exception\InvalidArgumentException(
            'Container must be a string alias or an instance of Mezzio\Navigation\ContainerInterface'
        );
    }
}
