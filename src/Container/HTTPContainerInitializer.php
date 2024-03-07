<?php

namespace Twix\Container;

use ReflectionClass;
use ReflectionException;
use Twix\Application\AppConfig;
use Twix\Application\Boot;
use Twix\Application\TwixClassRegistry;
use Twix\Events\Handler;
use Twix\Filesystem\ClassFinder;
use Twix\Filesystem\ClassInspector;
use Twix\Http\Get;
use Twix\Http\HttpRequest;
use Twix\Http\HttpRouter;
use Twix\Http\Method;
use Twix\Http\Post;
use Twix\Http\Route;
use Twix\Http\RouterConfig;
use Twix\Interfaces\ClassRegistry;
use Twix\Interfaces\Container;
use Twix\Interfaces\EventBus;
use Twix\Interfaces\Request;
use Twix\Interfaces\Router;
use Twix\Twix;

final readonly class HTTPContainerInitializer
{
    /**
     * @throws ReflectionException
     */
    public static function init(Container $container): Container
    {

        self::setupRouter($container);
        self::setupEventHandlers($container);

        return $container;
    }

    /**
     * Finds Controllers (Classes with Route attribute methods)
     * and add them to the router
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
