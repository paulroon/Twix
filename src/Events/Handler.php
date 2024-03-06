<?php

namespace Twix\Events;

use Attribute;

#[Attribute]
final readonly class Handler
{
    public function __construct(private string $eventClassName)
    {
    }

    public function getEventClassName(): string
    {
        return $this->eventClassName;
    }
}
