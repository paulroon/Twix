<?php

namespace Twix\Application;

use Throwable;
use Twix\Http\HttpResponder;
use Twix\Interfaces\Application;
use Twix\Interfaces\Container;
use Twix\Interfaces\Request;
use Twix\Interfaces\Router;

final readonly class HttpApplication implements Application
{
    public function __construct(
        private Container $container
    ) {}

    public function run(): void
    {
        try {
            $router = $this->container->get(Router::class);
            $request = $this->container->get(Request::class);


            $httpResponder = $this->container->get(HttpResponder::class);
            $httpResponder->send(
                $router->dispatch($request),
            );
        } catch (Throwable $e) {
            dd($e);
        }
    }
}