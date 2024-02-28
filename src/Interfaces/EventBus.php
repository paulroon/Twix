<?php

namespace Twix\Interfaces;

interface EventBus
{
    public function dispatch(Event $event): void;

    public function addHandler(mixed $eventName, \ReflectionMethod $reflectionMethod): void;
}