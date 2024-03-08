<?php

namespace Twix\Application;

use Throwable;
use Twix\Container\HTTPContainerInitializer;
use Twix\Events\ApplicationBootEvent;
use Twix\Events\HttpControllerEvent;
use Twix\Events\HttpErrorResponseEvent;
use Twix\Events\HttpRequestEvent;
use Twix\Events\HttpResponderEvent;
use Twix\Events\HttpResponseEvent;
use Twix\Events\HttpTerminationEvent;
use Twix\Interfaces\Application;
use Twix\Interfaces\Container;
use Twix\Interfaces\EventBus;

final readonly class HttpApplication implements Application
{
    public function __construct(
        private readonly Container $container,
        private readonly EventBus $eventBus
    ) {
        HTTPContainerInitializer::init($this->container);
    }

    public function run(): void
    {
        // Boot Application
        $this->eventBus->dispatch(new ApplicationBootEvent());

        // pre-controller event
        $this->eventBus->dispatch(new HttpRequestEvent());
        // Run Controller
        $this->eventBus->dispatch(new HttpControllerEvent());

        // Post-controller / Pre-responder event
        $this->eventBus->dispatch(new HttpResponseEvent());

        // Run Http Responder
        $this->eventBus->dispatch(new HttpResponderEvent());

        // Post responder / Terminator Event
        $this->eventBus->dispatch(new HttpTerminationEvent());
    }

    public function handleError(Throwable $throwable): void
    {
        // Run Http Error Responder
        $this->eventBus->dispatch(new HttpErrorResponseEvent($throwable));

        // Run Http Responder
        $this->eventBus->dispatch(new HttpResponderEvent());

        // Run Http Responder
        $this->eventBus->dispatch(new HttpResponderEvent());
    }
}
