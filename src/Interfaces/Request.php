<?php

namespace Twix\Interfaces;

use Twix\Http\Method;

interface Request
{
    public function getMethod(): Method;

    public function getUri(): string;

    public function getBody(): string;

    public function getHeaders(): array;
}
