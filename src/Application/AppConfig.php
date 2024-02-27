<?php

namespace Twix\Application;

final readonly class AppConfig
{
    public function __construct(
        private readonly string $root,
        private readonly string $env,
    ) {}

    public function getEnv(): string
    {
        return $this->env;
    }

    public function getRoot(): string
    {
        return $this->root;
    }
}