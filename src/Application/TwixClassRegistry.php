<?php

namespace Twix\Application;

use ReflectionAttribute;
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

        // TODO:: Cache?
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

            $className = $reflectionClass->getName();

            $this->classRegister[$className] = [];
            $this->classRegister[$className]['_interfaces'] = array_keys($reflectionClass->getInterfaces());
            $this->classRegister[$className]['_attributes'] = $reflectionClass->getAttributes();
            $this->classRegister[$className]['_methods'] = [];

            foreach ($reflectionClass->getMethods() as $reflectionMethod) {
                $methodName = $reflectionMethod->getName();

                $this->classRegister[$className]['_methods'][$methodName] = [];

                foreach($reflectionMethod->getAttributes() as $reflectionAttribute) {
                    $this->classRegister[$className]['_methods'][$methodName][$reflectionAttribute->getName()] = [...$reflectionAttribute->getArguments()];
                }

            }
        }
    }

    public function getRegisteredAttributes(...$attributes): array
    {
        $attributeMethods = [];
        foreach ($this->classRegister as $class => $classData) {
            foreach ($classData['_methods'] as $method => $attrs) {
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

    public function getBootableClasses(): array
    {
        return array_filter($this->classRegister, function ($classData, $className) {

            /** @var ReflectionAttribute $classDataAttr */
            foreach ($classData['_attributes'] as $classDataAttr) {
                if ($classDataAttr->getName() === Boot::class) {
                    return true;
                }
            }

            return false;
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getClassRegister(): array
    {
        return $this->classRegister;
    }
}
