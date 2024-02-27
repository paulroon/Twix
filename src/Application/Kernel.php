<?php

namespace Twix\Application;

use App\Controllers\HomeController;
use Twix\Http\GenericRouter;
use Twix\Http\HttpRequest;
use Twix\Http\RouterConfig;
use Twix\Interfaces\Container;
use Twix\Interfaces\Request;
use Twix\Interfaces\Response;
use Twix\Interfaces\Router;

final readonly class Kernel
{
    public function __construct(
        private readonly Container $container
    ) {}

    public function init(): Container
    {
        return $this->getContainer();
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    public function getAppConfig(): AppConfig
    {
        return $this->container->get(AppConfig::class);
    }
}