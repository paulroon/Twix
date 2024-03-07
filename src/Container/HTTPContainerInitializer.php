<?php

namespace Twix\Container;

use ReflectionException;
use Twix\Application\TwixClassRegistry;
use Twix\Events\Handler;
use Twix\Http\Get;
use Twix\Http\HttpRouter;
use Twix\Http\Post;
use Twix\Http\Route;
use Twix\Http\RouterConfig;
use Twix\Interfaces\ClassRegistry;
use Twix\Interfaces\Container;
use Twix\Interfaces\EventBus;
use Twix\Interfaces\Router;

final readonly class HTTPContainerInitializer
{
    public static function init(Container $container): Container
    {
        try {
            self::setupRouter($container);
            self::setupEventHandlers($container);
        } catch (\Throwable) {
            // App initialisation Error
        }

        return $container;
    }

    /**
     * @throws ReflectionException
     */
    private static function setupRouter(Container &$container): void
    {

        $register = $container->get(ClassRegistry::class);
        $routeFiltererRegister = $register->getRegisteredAttributes(
            Get::class,
            Post::class,
            Route::class
        );

        $container->singleton(
            RouterConfig::class,
            fn () => new RouterConfig(
                controller: array_map(fn (array $routeConfig) => $routeConfig['class'], $routeFiltererRegister)
            )
        );

        $container->singleton(
            Router::class,
            fn (Container $container) => new HttpRouter($container, $container->get(RouterConfig::class))
        );
    }

    /**
     * @throws ReflectionException
     */
    private static function setupEventHandlers(Container $container): void
    {
        /** @var TwixClassRegistry $register */
        $register = $container->get(ClassRegistry::class);

        /** @var EventBus $eventbus */
        $eventbus = $container->get(EventBus::class);

        $routeFiltererRegister = $register->getRegisteredAttributes(Handler::class);

        foreach ($routeFiltererRegister as $handler) {
            $eventbus->addHandler($handler['attrArgs'][0], new \ReflectionMethod($handler['class'], $handler['method']));
        }

    }
}
