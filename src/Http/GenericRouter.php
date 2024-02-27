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

                $params = $this->resolveParams($route->uri, $request->getUri());

                if (count($params) < 1 && $route->uri !== $request->getUri()) {
                    continue;
                }

                $controller = $this->container->get($controllerClass);
                return $controller->{$method->getName()}(...$params);
            }
        }

        return new HttpResponse(Status::HTTP_404, "NOT FOUND");
    }

    private function resolveParams(string $routeUri, string $requestUri): array
    {
        $result = preg_match_all('/\{\w+}/', $routeUri, $tokens);

        if (!$result) {
            return [];
        }
        $tokens = $tokens[0];

        $matchingRegExp = '/^' . str_replace(
            ['/', ...$tokens],
            ['\\/', ...array_fill(0, count($tokens), '([\w\d\s]+)')],
            $routeUri
        ) . '$/';

        $result = preg_match_all($matchingRegExp, $requestUri, $matches);

        if ($result === 0) {
            return [];
        }
        unset($matches[0]);

        $matches = array_values($matches);

        $params = [];
        foreach ($matches as $i => $match) {
            $params[trim($tokens[$i], '{}')] = $match[0];
        }

        return $params;
    }
}