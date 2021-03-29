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

use Interop\Container\ContainerInterface as InteropContainerInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Factory\InvokableFactory;

/**
 * Plugin manager implementation for navigation helpers
 *
 * Enforces that helpers retrieved are instances of
 * Navigation\HelperInterface. Additionally, it registers a number of default
 * helpers.
 */
final class PluginManager extends AbstractPluginManager implements InteropContainerInterface
{
    /**
     * @var string
     *             Valid instance types
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $instanceOf = HelperInterface::class;

    /**
     * Default factories
     *
     * @var array<string, string>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $factories = [
        AcceptHelperInterface::class => AcceptHelperFactory::class,
        ContainerParserInterface::class => ContainerParserFactory::class,
        ConvertToPagesInterface::class => ConvertToPagesFactory::class,
        FindActiveInterface::class => FindActiveFactory::class,
        FindFromPropertyInterface::class => FindFromPropertyFactory::class,
        FindRoot::class => InvokableFactory::class,
        HtmlifyInterface::class => HtmlifyFactory::class,
        HtmlElementInterface::class => HtmlElementFactory::class,
    ];

    /**
     * @var array<string, string>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $aliases = [
        FindRootInterface::class => FindRoot::class,
    ];
}
