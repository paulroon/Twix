<?php

namespace Twix\Interfaces;

interface Container
{
    public function register(string $classname, callable $definition): self;

    public function isRegistered(string $classname): bool;

    public function singleton(string $classname, callable $definition): self;

    /**
     * @template TClassName
     * @param class-string<TClassName> $classname
     * @return TClassName
     */
    public function get(string $classname): object;

}