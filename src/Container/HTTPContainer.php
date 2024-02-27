<?php

namespace Twix\Container;

use App\Controllers\HomeController;
use Twix\Application\AppConfig;
use Twix\Filesystem\ClassFinder;
use Twix\Http\GenericRouter;
use Twix\Http\HttpRequest;
use Twix\Http\Method;
use Twix\Http\RouterConfig;
use Twix\Interfaces\Container;
use Twix\Interfaces\Request;
use Twix\Interfaces\Router;

final readonly class HTTPContainer
{
    public static function init(Container $container): Container
    {
        $controllers = ClassFinder::List(sprintf(
            "%s/app/Controllers",
            $container->get(AppConfig::class)->getRoot()
        ));

        $container->singleton(
            RouterConfig::class,
            fn () => new RouterConfig(
                controller: $controllers
            ));

        $container->singleton(
            Router::class,
            fn (Container $container) => new GenericRouter($container, $container->get(RouterConfig::class))
        );

        $method = Method::tryFrom($_SERVER['REQUEST_METHOD']) ?? Method::GET;
        $container->register(Request::class, fn () => new HttpRequest(
            method: $method,
            uri: $_SERVER['REQUEST_URI'] ?? '/',
            body: match($method) {
                Method::POST => $_POST,
                default => $_GET,
            }
        ));

        return $container;
    }
}