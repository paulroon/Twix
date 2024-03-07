<?php

namespace Twix\Application;

use ReflectionClass;
use ReflectionException;
use Twix\Filesystem\ClassFinder;
use Twix\Twix;

final class TwixClassRegistry
{

    private array $classRegister = [];

    /**
     * @throws ReflectionException
     */
    public function __construct()
    {
        $container = Twix::getContainer();
        $appConfig = $container->get(AppConfig::class);

        $this->addClassesToRegister(ClassFinder::findClassesInDir($appConfig->getAppPath()));
        $this->addClassesToRegister(ClassFinder::findClassesInDir($appConfig->getTwixRoot()));

    }

    /**
     * @throws ReflectionException
     */
    private function addClassesToRegister(array $classNames): void
    {

        foreach($classNames as $className) {
            $reflectionClass = new ReflectionClass($className);

            $this->classRegister[$reflectionClass->getName()] = [];

            foreach ($reflectionClass->getMethods() as $reflectionMethod) {
                $methodName = $reflectionMethod->getName();

                $this->classRegister[$reflectionClass->getName()][$methodName] = [];

                foreach($reflectionMethod->getAttributes() as $reflectionAttribute) {
                    $this->classRegister[$reflectionClass->getName()][$methodName][$reflectionAttribute->getName()] = [...$reflectionAttribute->getArguments()];
                }

            }
        }
    }

    public function getRegisteredAttributes(...$attributes): array
    {
        $attributeMethods = [];
        foreach ($this->classRegister as $class => $methods) {
            foreach ($methods as $method => $attrs) {
                foreach ($attrs as $attrClassName => $attrArgs) {
                    if (in_array($attrClassName, $attributes)) {
                        $attributeMethods[] = [
                            'class' => $class,
                            'method' => $method,
                            'attrClassName' => $attrClassName,
                            'attrArgs' => $attrArgs,
                        ];
                    }
                }
            }
        }
        return $attributeMethods;
    }

    public function getClassRegister(): array
    {
        return $this->classRegister;
    }
}