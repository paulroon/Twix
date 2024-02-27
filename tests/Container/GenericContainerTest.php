<?php

namespace Twix\Test\Container;

use PHPUnit\Framework\TestCase;
use Twix\Container\GenericContainer;
use Twix\Exceptions\ContainerException;

class GenericContainerTest extends TestCase
{

    /** @test */
    public function testContainer()
    {
        $container = new GenericContainer();

        $container->register(ThingA::class, fn () => new ThingA());

        $instance = $container->get(ThingA::class);

        $this->assertInstanceOf(ThingA::class, $instance);
    }

    /** @test */
    public function testContainerAutowire()
    {
        $container = new GenericContainer();

        $instance = $container->get(ThingA::class);

        $this->assertInstanceOf(ThingA::class, $instance);
    }

    /** @test */
    public function testContainerWithDependencies()
    {
        $container = new GenericContainer();

        $instance = $container->get(ThingB::class);

        $this->assertInstanceOf(ThingB::class, $instance);
    }

    /** @test */
    public function testContainerWithUndefinedDependencies()
    {
        $container = new GenericContainer();

        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage("Cannot Autowire Twix\Test\Container\ThingC:: Dependency [subThing1] has no class type definition.");
        $container->get(ThingC::class);
    }

    public function testSingleton()
    {
        $container = new GenericContainer();

        $instance = $container
            ->singleton(SingletonThing::class)
            ->get(SingletonThing::class);

        $this->assertInstanceOf(SingletonThing::class, $instance);
        $this->assertEquals(1, $instance::$count);

        $instance = $container
            ->get(SingletonThing::class);
        $this->assertEquals(1, $instance::$count);
    }
}

class ThingA {}

class ThingB {
    public function __construct(
        private SubThing1 $subThing
    ) {}
}

class ThingC {
    public function __construct(
        private $subThing1,
        private SubThing2 $subThing2,
    ) {}
}

class SubThing1 {}
class SubThing2 {}

class SingletonThing {
    public static $count = 0;
    public function __construct()
    {
        self::$count++;
    }
}
