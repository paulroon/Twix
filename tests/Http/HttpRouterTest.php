<?php

namespace Twix\Test\Http;

use Twix\Http\HttpRequest;
use Twix\Http\HttpRouter;
use Twix\Http\Method;
use Twix\Http\RouterConfig;
use Twix\Http\Status;
use Twix\Interfaces\Container;
use Twix\Interfaces\Router;
use Twix\Test\TestCase;

class HttpRouterTest extends TestCase
{
    public function setup(): void
    {
        parent::setup();
        $this->container->singleton(
            RouterConfig::class,
            fn () => new RouterConfig(
                controller: [
                    Fixtures\TestController::class,
                ]
            )
        );

        $this->container->singleton(
            Router::class,
            fn (Container $container) => new HttpRouter($container, $container->get(RouterConfig::class))
        );
    }

    /**
     * @test
     */
    public function testRouterBasicRoute()
    {
        $router = $this->container->get(Router::class);

        $response = $router->dispatch(
            new HttpRequest(
                method: Method::GET,
                uri: '/'
            )
        );

        $this->assertSame($router::class, HttpRouter::class);
        $this->assertSame(Status::HTTP_200, $response->getStatus());
        $this->assertSame('Hello World!', $response->getBody());
    }

    /**
     * @test
     */
    public function testRouterWithMissingRoute()
    {
        $router = $this->container->get(Router::class);

        $response = $router->dispatch(
            new HttpRequest(
                method: Method::GET,
                uri: '/this/does/not/exist'
            )
        );

        $this->assertSame($router::class, HttpRouter::class);
        $this->assertSame(Status::HTTP_404, $response->getStatus());
    }

    /**
     * @test
     */
    public function testRouterWithParams()
    {
        $router = $this->container->get(Router::class);

        $response = $router->dispatch(
            new HttpRequest(
                method: Method::GET,
                uri: '/greet/freddy'
            )
        );

        $this->assertSame($router::class, HttpRouter::class);
        $this->assertSame(Status::HTTP_200, $response->getStatus());
        $this->assertSame('Hello freddy!', $response->getBody());
    }

    /**
     * @test
     */
    public function testRouterWithMultipleParams()
    {
        $router = $this->container->get(Router::class);

        $response = $router->dispatch(
            new HttpRequest(
                method: Method::GET,
                uri: '/greet/freddy/with/mug'
            )
        );

        $this->assertSame($router::class, HttpRouter::class);
        $this->assertSame(Status::HTTP_200, $response->getStatus());
        $this->assertSame("Hello freddy, here's a mug!", $response->getBody());
    }
}
