<?php

namespace Twix\Test\Http;

use Twix\Exceptions\ContainerException;
use Twix\Http\GenericRouter;
use Twix\Http\HttpRequest;
use Twix\Http\Method;
use Twix\Http\Status;
use Twix\Interfaces\Router;
use Twix\Test\TestCase;

class GenericRouterTest extends TestCase
{
    /**
     * @test
     * @throws ContainerException
     */
    public function testRouter()
    {
        $router = $this->container->get(Router::class);

        $response = $router->dispatch(new HttpRequest(
            method: Method::GET,
            uri: '/home')
        );

        $this->assertSame($router::class, GenericRouter::class);
        $this->assertSame(Status::HTTP_200, $response->getStatus());
        $this->assertSame("Hello World!", $response->getBody());
    }
}
