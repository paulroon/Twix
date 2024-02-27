<?php

namespace Twix\Http;

use Twix\Interfaces\Response;

final readonly class HttpResponse implements Response
{
    public function __construct(
        private Status $status,
        private string $body
    ) {}

    public function getBody(): string
    {
        return $this->body;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }
}