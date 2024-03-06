<?php

namespace Twix\Test;

use Twix\Container\TwixContainer;
use Twix\Exceptions\ContainerException;
use Twix\Http\Get;
use Twix\Http\HttpResponse;
use Twix\Http\HttpRouter;
use Twix\Http\RouterConfig;
use Twix\Http\Status;
use Twix\Interfaces\Container;
use Twix\Interfaces\Response;
use Twix\Interfaces\Router;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Container $container;

    /**
     * @throws ContainerException
     */
    public function setup(): void
    {
        $this->container = new TwixContainer();

        $this->container->singleton(Container::class, fn () => $this->container);

        $this->container->singleton(
            RouterConfig::class,
            fn () => new RouterConfig(
                controller: [
                    TestController::class,
                ]
            )
        );

        $this->container->singleton(
            Router::class,
            fn (Container $container) => new HttpRouter($container, $container->get(RouterConfig::class))
        );
    }
}

final readonly class TestController
{
    #[Get('/')]
    public function index(): Response
    {
        return new HttpResponse(Status::HTTP_200, "Hello World!");
    }

    #[Get('/greet/{name}')]
    public function show(string $name): Response
    {
        return new HttpResponse(Status::HTTP_200, sprintf("Hello %s!", $name));
    }

    #[Get('/greet/{name}/with/{thing}')]
    public function show2(string $name, string $thing): Response
    {
        return new HttpResponse(Status::HTTP_200, sprintf("Hello %s, here's a %s!", $name, $thing));
    }
}
