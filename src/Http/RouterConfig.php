<?php

namespace Twix\Http;

final readonly class RouterConfig
{
    public function __construct(
        public array $controller = []
    ) {
    }
}
