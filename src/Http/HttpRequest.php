<?php

namespace Twix\Http;

use Twix\Interfaces\Request;

final readonly class HttpRequest implements Request
{
    public function __construct(
        private Method $method,
        private string $uri,
        private array $body = [],
        private array $headers = [],
    ) {
    }

    public function getMethod(): Method
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
