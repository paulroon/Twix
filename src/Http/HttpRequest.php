<?php

namespace Twix\Http;

use Twix\Interfaces\Request;

final readonly class HttpRequest implements Request
{
    public function __construct(
        private Method $method,
        private string $uri,
        private string $body = '',
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

    public function getBody(): string
    {
        return $this->body;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $key = null): string
    {
        return $this->headers[$key] ?? '';
    }

    public function describe(): string
    {
        return sprintf('%s %s [%s]', $this->getMethod()->value, $this->getUri(), $this->getBody());
    }

    public function isJson()
    {

    }
}
