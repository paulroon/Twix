<?php

namespace Twix\Application;

use Twix\Interfaces\Container;

final readonly class Kernel
{
    public function __construct(
        private Container $container
    ) {
    }

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
