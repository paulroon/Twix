<?php

namespace Twix\Filesystem;

use ReflectionClass;
use ReflectionException;

final readonly class ClassInspector
{
    /**
     * Checks to see if the supplied class contains a method with an (or by filtered list) attribute(s)
     *
     * @param string $classname
     * @param array $filterAttributes - empty is ANY
     * @return bool
     * @throws ReflectionException
     */
    public static function HasMethodWithAttribute(string $classname, array $filterAttributes = []): bool
    {
        $reflectionClass = new ReflectionClass($classname);

        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            $attributes = array_map(fn (\ReflectionAttribute $attr) => $attr->getName(), $reflectionMethod->getAttributes());

            $hasAttributeUnFiltered = (count($attributes) > 0 && count($filterAttributes) == 0);
            $hasAttributeInFilter = (count(array_intersect($attributes, $filterAttributes)) > 0);
            if ($hasAttributeUnFiltered || $hasAttributeInFilter) {
                return true;
            }
        }
        return false;
    }
}