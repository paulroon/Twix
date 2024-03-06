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

    }
}

