<?php

namespace Twix\Application;

use Attribute;

/**
 * When an application class is attributed with 'Boot' the static ::boot method will be called instead of the 'new Class' as the register function in the container
 * this allows for application classes to set up their own configuration
 */
#[Attribute]
final readonly class Boot
{
    public function __construct(private string $staticMethodName)
    {
    }

    public function getStaticMethodName(): string
    {
        return $this->staticMethodName;
    }
}
