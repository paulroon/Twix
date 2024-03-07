<?php

namespace Twix\Application;

use Throwable;
use Twix\Container\HTTPContainerInitializer;
use Twix\Events\ApplicationBootEvent;
use Twix\Events\HttpControllerEvent;
use Twix\Events\HttpErrorResponse;
use Twix\Events\HttpRequestEvent;
use Twix\Events\HttpResponderEvent;
use Twix\Events\HttpResponseEvent;
use Twix\Events\HttpTerminationEvent;
use Twix\Interfaces\Application;
use Twix\Interfaces\EventBus;
use Twix\Twix;

final readonly class HttpApplication implements Application
{
    public function __construct()
    {
        HTTPContainerInitializer::init(Twix::getContainer());
    }

    public function run(): void
    {
        $container = Twix::getContainer();

        // Boot Application
        $container->get(EventBus::class)->dispatch(new ApplicationBootEvent());

        // pre-controller event
        $container->get(EventBus::class)->dispatch(new HttpRequestEvent());

        // Run Controller
        $container->get(EventBus::class)->dispatch(new HttpControllerEvent());

        // Post-controller / Pre-responder event
        $container->get(EventBus::class)->dispatch(new HttpResponseEvent());

        // Run Http Responder
        $container->get(EventBus::class)->dispatch(new HttpResponderEvent());

        // Post responder / Terminator Event
        $container->get(EventBus::class)->dispatch(new HttpTerminationEvent());
    }

    public function handleError(Throwable $throwable): void
    {
        $container = Twix::getContainer();

        // Run Http Error Responder
        $container->get(EventBus::class)->dispatch(new HttpErrorResponse($throwable));

        // Run Http Responder
        $container->get(EventBus::class)->dispatch(new HttpResponderEvent());
    }
}
