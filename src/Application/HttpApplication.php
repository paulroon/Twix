<?php

namespace Twix\Application;

use Throwable;
use Twix\Events\HttpControllerEvent;
use Twix\Events\HttpErrorResponse;
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
        private Container $container
    ) {
    }

    public function run(): void
    {
        // pre-controller event
        $this->container->get(EventBus::class)->dispatch(new HttpRequestEvent());

        // Run Controller
        $this->container->get(EventBus::class)->dispatch(new HttpControllerEvent());

        // Post-controller / Pre-responder event
        $this->container->get(EventBus::class)->dispatch(new HttpResponseEvent());

        // Run Http Responder
        $this->container->get(EventBus::class)->dispatch(new HttpResponderEvent());

        // Post responder / Terminator Event
        $this->container->get(EventBus::class)->dispatch(new HttpTerminationEvent());
    }

    public function handleError(Throwable $throwable): void
    {
        // Run Http Error Responder
        $this->container->get(EventBus::class)->dispatch(new HttpErrorResponse($throwable));

        // Run Http Responder
        $this->container->get(EventBus::class)->dispatch(new HttpResponderEvent());
    }
}
