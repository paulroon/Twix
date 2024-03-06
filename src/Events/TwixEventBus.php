<?php

namespace Twix\Events;

use ReflectionException;
use ReflectionMethod;
use Twix\Interfaces\Event;
use Twix\Interfaces\EventBus;
use Twix\Twix;

final class TwixEventBus implements EventBus
{
    private array $handlers = [];

    /**
     * @throws ReflectionException
     */
    public function dispatch(Event $event): void
    {
        $eventHandlers = $this->handlers[$event::class] ?? [];

        /** @var ReflectionMethod $reflectionMethod */
        foreach ($eventHandlers as $reflectionMethod) {

            $handler = Twix::getContainer()->get($reflectionMethod->getDeclaringClass()->getName());

            $reflectionMethod->invoke($handler, $event);
        }
    }

    public function addHandler(mixed $eventName, \ReflectionMethod $reflectionMethod): void
    {
        $this->handlers[$eventName] = [...($this->handlers[$eventName] ?? []), $reflectionMethod];
    }
}