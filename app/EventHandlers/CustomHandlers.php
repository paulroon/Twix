<?php

namespace App\EventHandlers;

use App\Events\CustomEvent;
use Twix\Events\Handler;

final readonly class CustomHandlers
{
    #[Handler(CustomEvent::class)]
    public function customEventHandler(): void
    {
        dump('handling CustomHandlers::customEventHandler()');
    }

    public function other(): void
    {
        dump('nothing');
    }
}