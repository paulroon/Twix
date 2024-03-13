<?php

namespace Twix\Application;

use Twix\Interfaces\ClassRegistry;
use Twix\Interfaces\Container;

final readonly class Kernel
{
    public function __construct(
        private Container $container
    ) {
    }

    public function init(): Container
    {
        $this->registerBootLoaders();

        return $this->getContainer();
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    // inspects known classes in the register for 'Boot' attributes and registers them in the container
    private function registerBootLoaders(): void
    {
        /** @var TwixClassRegistry $register */
        $register = $this->getContainer()->get(ClassRegistry::class);

        foreach ($register->getBootableClasses() as $bootableClassName => $bootableClass) {

            /** @var \ReflectionAttribute $classAttribute */
            foreach ($bootableClass['_attributes'] as $classAttribute) {
                if ($classAttribute->getName() == Boot::class) {
                    $bootMethod = $classAttribute->getArguments()[0];
                    $this->getContainer()->register($bootableClassName, fn () => $bootableClassName::$bootMethod());
                }
            }

        }

    }
}
