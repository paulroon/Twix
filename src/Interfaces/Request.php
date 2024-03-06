<?php

namespace Twix\Interfaces;

use Twix\Http\Method;

interface Request
{
    public function getMethod(): Method;

    public function getUri(): string;

    public function getBody(): array;

    public function getHeaders(): array;
}
