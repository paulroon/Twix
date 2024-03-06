<?php

namespace Twix\Interfaces;

use Twix\Http\Status;

interface Response
{
    public function getStatus(): Status;

    public function status(Status $status): self;

    public function getBody(): string|array;

    public function body(string|array $body): self;

    public function getHeaders(): array;

    public function addHeader(string $key, string $value): self;
}
