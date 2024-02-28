<?php

namespace Twix\Container;

use App\Rando;
use ReflectionException;
use Twix\Application\AppConfig;
use Twix\Filesystem\ClassFinder;
use Twix\Filesystem\ClassInspector;
use Twix\Http\Get;
use Twix\Http\HttpRouter;
use Twix\Http\HttpRequest;
use Twix\Http\Method;
use Twix\Http\Post;
use Twix\Http\Route;
use Twix\Http\RouterConfig;
use Twix\Interfaces\Container;
use Twix\Interfaces\Request;
use Twix\Interfaces\Router;

final readonly class HTTPContainerInitializer
{
    /**
     * @throws ReflectionException
     */
    public static function init(Container $container): Container
    {
        $applicationClasses = ClassFinder::findClassesInDir(sprintf(
            "%s/app",
            $container->get(AppConfig::class)->getRoot()
        ));


        self::setupRouter($applicationClasses, $container);
        self::buildRequest($container);

        return $container;
    }

    /**
     * Finds Controllers (Classes with Route attribute methods)
     * and add them to the router
     * @throws ReflectionException
     */
    private static function setupRouter(array $applicationClasses, Container &$container): void
    {
        $controllers = array_filter(
            $applicationClasses,
            fn (string $controllerClass) => ClassInspector::HasMethodWithAttribute($controllerClass, [
                Get::class, Post::class, Route::class
            ])
        );

        $container->singleton(
            RouterConfig::class,
            fn () => new RouterConfig(
                controller: $controllers
            ));

        $container->singleton(
            Router::class,
            fn (Container $container) => new HttpRouter($container, $container->get(RouterConfig::class))
        );
    }

    /**
     * Build The HTTP Request
     */
    private static function buildRequest(Container &$container): void
    {
        $method = Method::tryFrom($_SERVER['REQUEST_METHOD']) ?? Method::GET;
        $container->register(Request::class, fn () => new HttpRequest(
            method: $method,
            uri: $_SERVER['REQUEST_URI'] ?? '/',
            body: match($method) {
                Method::POST => $_POST,
                default => $_GET,
            }
        ));
    }
}