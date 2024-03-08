<?php

namespace Twix\Events;

use ReflectionException;
use ReflectionMethod;
use Twix\Interfaces\Container;
use Twix\Interfaces\Event;
use Twix\Interfaces\EventBus;

final class TwixEventBus implements EventBus
{
    private array $handlers = [];

    public function __construct(
        private readonly Container $container
    ) {
    }

    /**
     * @throws ReflectionException
     */
    public function dispatch(Event $event): void
    {
        $eventHandlers = $this->handlers[$event::class] ?? [];

        /** @var ReflectionMethod $reflectionMethod */
        foreach ($eventHandlers as $reflectionMethod) {

            $handlerClass = $this->container->get($reflectionMethod->getDeclaringClass()->getName());
            $handlerMethod = $reflectionMethod->getName();

            // invoke
            $handlerClass->$handlerMethod($event);
        }
    }

    public function addHandler(mixed $eventName, \ReflectionMethod $reflectionMethod): void
    {
        $this->handlers[$eventName] = [...($this->handlers[$eventName] ?? []), $reflectionMethod];
    }
}
