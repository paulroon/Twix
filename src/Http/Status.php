<?php

namespace Twix\Http;

enum Status: int
{
    case HTTP_200 = 200;
    case HTTP_201 = 201;
    case HTTP_404 = 404;
    case HTTP_500 = 500;

    public function isSuccessful(): bool
    {
        return 200 <= $this->value && $this->value < 300;
    }
}
