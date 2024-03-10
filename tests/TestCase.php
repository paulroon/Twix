<?php

namespace Twix\Test;

use Twix\Interfaces\Container;
use Twix\Twix;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected Container $container;

    public function setup(): void
    {
        Twix::boot(__DIR__ . '../', [
            'env' => 'test',
            'appDir' => 'tests'
        ])->minimal()->run();
        $this->container = Twix::getContainer();
    }

    protected function get(string $classname): mixed
    {
        return $this->container->get($classname);
    }
}
