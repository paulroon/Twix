<?php

namespace Twix\Container;

use ReflectionException;
use ReflectionParameter;
use Twix\Exceptions\ContainerException;
use Twix\Interfaces\Container;


final class GenericContainer implements Container
{
    /**
     * @var callable[]|array
     */
    private array $definitions = [];

    /**
     * @var object[]|array
     */
    private array $singletons = [];


    public function register(string $classname, callable $definition): Container
    {
        $this->definitions[$classname] = $definition;

        return $this;
    }

    /**
     * @throws ContainerException
     * @throws ReflectionException
     */
    public function singleton(string $classname, callable $definition = null): Container
    {
        $this->register($classname, $definition ?? $this->autowire_definition($classname));
        $this->singletons[$classname] = false;

        return $this;
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    public function get(string $classname): object
    {
        if (isset($this->singletons[$classname])) {
            if ($this->singletons[$classname] === false) {
                $definition = $this->definitions[$classname];
                $this->singletons[$classname] = $definition();
            }
            $instance = $this->singletons[$classname];

        } else {
            $definition = $this->definitions[$classname] ?? $this->autowire_definition($classname);
            $instance =  $definition();
        }

        return $instance;
    }

    /**
     * @throws ReflectionException
     * @throws ContainerException
     */
    private function autowire_definition(string $classname): callable
    {
        $reflectionClass = new \ReflectionClass($classname);

        $constructorParams = array_map(
            function (ReflectionParameter $cParam) use ($classname) {
                $dependencyClassName = $cParam->getType()?->getName();

                if (!$dependencyClassName) {
                    $paramName = $cParam->getName();
                    throw new ContainerException(sprintf("Cannot Autowire %s:: Dependency [%s] has no class type definition.", $classname, $paramName));
                }

                return $this->get($dependencyClassName);
            },
            $reflectionClass->getConstructor()?->getParameters() ?? []
        );

        return fn () => new $classname(...$constructorParams);
    }
}