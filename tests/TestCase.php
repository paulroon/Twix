<?php

namespace Twix\Test;

use App\Controllers\HomeController;
use Twix\Container\GenericContainer;
use Twix\Exceptions\ContainerException;
use Twix\Http\GenericRouter;
use Twix\Http\RouterConfig;
use Twix\Interfaces\Container;
use Twix\Interfaces\Router;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Container $container;

    /**
     * @throws ContainerException
     */
    public function setup(): void
    {
        $this->container = new GenericContainer();

        $this->container->singleton(Container::class, fn () => $this->container);

        $this->container->singleton(
            RouterConfig::class,
            fn () => new RouterConfig(
                controller: [
                    HomeController::class,
                ]
            ));

        $this->container->singleton(
            Router::class,
            fn (Container $container) => new GenericRouter($container, $container->get(RouterConfig::class))
        );
    }
}