<?php

namespace Twix\Container;

use ReflectionException;
use ReflectionParameter;
use Twix\Exceptions\ContainerException;
use Twix\Interfaces\Container;

final class TwixContainer implements Container
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

    public function isRegistered(string $classname): bool
    {
        return isset($this->definitions[$classname]);
    }

    /**
     * @throws ContainerException
     */
    public function singleton(string $classname, callable $definition = null): Container
    {
        $this->register($classname, $definition ?? $this->autowire_definition($classname));
        $this->singletons[$classname] = false;

        return $this;
    }

    /**
     * @throws ContainerException
     */
    public function get(string $classname): object
    {
        if (isset($this->singletons[$classname])) {
            if ($this->singletons[$classname] === false) {
                $definition = $this->definitions[$classname];
                $this->singletons[$classname] = $definition($this);
            }
            $instance = $this->singletons[$classname];

        } else {
            $definition = $this->definitions[$classname] ?? $this->autowire_definition($classname);
            $instance = $definition($this);
        }

        return $instance;
    }

    /**
     * @throws ContainerException
     */
    private function autowire_definition(string $classname): callable
    {
        try {
            $reflectionClass = new \ReflectionClass($classname);

            $constructorParams = array_map(
                function (ReflectionParameter $cParam) use ($classname) {
                    $dependencyClassName = $cParam->getType()?->getName();

                    if (! $dependencyClassName) {
                        $paramName = $cParam->getName();

                        throw new ContainerException(sprintf('Cannot Autowire %s:: Dependency [%s] has no class type definition.', $classname, $paramName));
                    }

                    return $this->get($dependencyClassName);
                },
                $reflectionClass->getConstructor()?->getParameters() ?? []
            );

            return fn () => new $classname(...$constructorParams);
        } catch (ReflectionException $e) {
            throw new ContainerException(sprintf('There was a problem with autowire inspection for class %s', $classname));
        }

    }
}
