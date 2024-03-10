<?php

namespace Twix\Application;

use Twix\Interfaces\Application;
use Twix\Interfaces\Container;
use Twix\Interfaces\EventBus;

final readonly class MinimalApplication implements Application
{
    public function __construct(
        private readonly Container $container,
        private readonly EventBus $eventBus
    ) {
    }

    public function run(): void
    {
    }
}
