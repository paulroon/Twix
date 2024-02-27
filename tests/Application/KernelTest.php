<?php

namespace Twix\Test\Application;

use PHPUnit\Framework\TestCase;
use Twix\Application\Kernel;
use Twix\Container\GenericContainer;
use Twix\Interfaces\Container;

class KernelTest extends TestCase
{
    /** @test */
    public function testConstruct()
    {
        $kernel = new Kernel(new GenericContainer());

        $this->assertSame($kernel::class, Kernel::class);
        $this->assertInstanceOf(Container::class, $kernel->getContainer());
    }

}
