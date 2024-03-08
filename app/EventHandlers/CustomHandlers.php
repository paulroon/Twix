<?php

namespace App\EventHandlers;

use App\Events\CustomEvent;
use Twix\Events\Handler;
use Twix\Events\HttpErrorResponseEvent;
use Twix\Http\HttpResponse;
use Twix\Http\Status;
use Twix\Interfaces\Response;
use Twix\Twix;

final readonly class CustomHandlers
{
    #[Handler(CustomEvent::class)]
    public function customEventHandler(): void
    {
        dump('handling CustomHandlers::customEventHandler()');
    }

    #[Handler(HttpErrorResponseEvent::class)]
    public function customErrorResponseHandler(HttpErrorResponseEvent $httpErrorResponse): void
    {
        $throwable = $httpErrorResponse->getThrowable();

        $httpErrorResponse = new HttpResponse(
            status: Status::HTTP_404,
            body: 'Custom:: ' . $throwable->getMessage(),
            headers: []
        );

        Twix::getContainer()->register(Response::class, fn () => $httpErrorResponse);
    }

    public function other(): void
    {
        dump('nothing');
    }
}
