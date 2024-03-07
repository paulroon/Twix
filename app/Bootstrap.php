<?php

namespace App;

use Twix\Application\Boot;
use Twix\Events\Handler;
use Twix\Events\HttpErrorResponse;

final readonly class Bootstrap
{
    #[Boot]
    public function bootstrap(): void
    {
        dump('do it here');
    }

    #[Handler(HttpErrorResponse::class)]
    public function other(): void
    {
        dump('no fnaks!');
    }
}
