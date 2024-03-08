<?php

namespace Twix\Test;

use Twix\Events\TwixEventBus;
use Twix\Exceptions\ContainerException;
use Twix\Interfaces\Container;
use Twix\Interfaces\EventBus;
use Twix\Twix;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Container $container;

    /**
     * @throws ContainerException
     */
    public function setup(): void
    {
        Twix::boot(__DIR__ . '../');
        $this->container = Twix::getContainer();

        //        $this->container->singleton(Container::class, fn () => $this->container);
        //        $this->container->singleton(EventBus::class, fn () => new TwixEventBus());

    }
}
