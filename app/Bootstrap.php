<?php

namespace App;

use Twix\Application\Boot;
use Twix\Events\ApplicationBootEvent;
use Twix\Events\Handler;
use Twix\Events\HttpErrorResponse;
use Twix\Interfaces\Logger;
use Twix\Interfaces\Router;
use Twix\Twix;

final readonly class Bootstrap
{
    #[Handler(ApplicationBootEvent::class)]
    public function bootstrap(): void
    {
        Twix::getContainer()->get(Logger::class)->debug("Bootstrapping Application");
    }

    #[Handler(HttpErrorResponse::class)]
    public function other(): void
    {
        dump('Error!!!!!');
        debug_print_backtrace();
    }
}
