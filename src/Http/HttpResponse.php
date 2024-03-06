<?php

namespace Twix\Http;

use Twix\Interfaces\Response;

final class HttpResponse implements Response
{
    public function __construct(
        public Status $status,
        public string|array $body,
        public array $headers = []
    ) {
    }

    public function getBody(): string|array
    {
        return $this->body;
    }

    public function body(string|array $body): HttpResponse
    {
        $this->body = $body;

        return $this;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function status(Status $status): HttpResponse
    {
        $this->status = $status;

        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function addHeader(string $key, string $value): HttpResponse
    {
        $this->headers[$key] = $value;

        return $this;
    }
}
