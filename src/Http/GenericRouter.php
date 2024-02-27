<?php

namespace Twix\Http;

use ReflectionException;
use Twix\Interfaces\Container;
use Twix\Interfaces\Request;
use Twix\Interfaces\Response;
use Twix\Interfaces\Router;

final class GenericRouter implements Router
{

    public function __construct(
        private Container $container,
        private RouterConfig $routerConfig
    ) { }

    /**
     * @throws ReflectionException
     */
    public function dispatch(Request $request): Response
    {
        foreach ($this->routerConfig->controller as $controllerClass) {
            $reflectionClass = new \ReflectionClass($controllerClass);

            foreach ($reflectionClass->getMethods() as $method) {
                $attribute = $method->getAttributes(Route::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;

                if (!$attribute) {
                    continue;
                }

                /** @var Route $route */
                $route = $attribute->newInstance();
                if ($route->method !== $request->getMethod()) {
                    continue;
                }

                if ($route->uri !== $request->getUri()) {
                    continue;
                }

                $controller = $this->container->get($controllerClass);
                return $controller->{$method->getName()}();
            }
        }

        return new HttpResponse(Status::HTTP_404, "NOT FOUND");
    }



    public function getRouterConfig(): RouterConfig
    {
        return $this->routerConfig;
    }
}