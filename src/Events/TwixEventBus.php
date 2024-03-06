<?php

namespace Twix\Events;

use ReflectionException;
use ReflectionMethod;
use Twix\Interfaces\Event;
use Twix\Interfaces\EventBus;
use Twix\Interfaces\Logger;
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
        $container = Twix::getContainer();
        $logger = $container->get(Logger::class);

        /** @var ReflectionMethod $reflectionMethod */
        foreach ($eventHandlers as $reflectionMethod) {

            $handler = $container->get($reflectionMethod->getDeclaringClass()->getName());

            $logger->info(sprintf("EventHandler: %s::%s(%s)", $handler::class, $reflectionMethod->getName(), $event::class));

            $reflectionMethod->invoke($handler, $event);
        }
    }

    public function addHandler(mixed $eventName, \ReflectionMethod $reflectionMethod): void
    {
        $this->handlers[$eventName] = [...($this->handlers[$eventName] ?? []), $reflectionMethod];
    }
}
