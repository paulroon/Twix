<?php

namespace Twix\Events;

use Twix\Http\HttpRequest;

final readonly class HttpLifecycle
{
    #[Handler(HttpRequest::class)]
    public function handleIncomingRequest(HttpRequest $request): void
    {
        dump($request);
    }
}