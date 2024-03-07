<?php

namespace Twix\Test;

use Twix\Container\TwixContainer;
use Twix\Exceptions\ContainerException;
use Twix\Interfaces\Container;

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
