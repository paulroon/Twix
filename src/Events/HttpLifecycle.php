<?php

namespace Twix\Events;

use Twix\Http\HttpRequest;
use Twix\Http\HttpResponder;
use Twix\Http\HttpResponse;
use Twix\Http\Method;
use Twix\Http\Status;
use Twix\Interfaces\Request;
use Twix\Interfaces\Response;
use Twix\Interfaces\Router;
use Twix\Twix;

final readonly class HttpLifecycle
{
    #[Handler(HttpRequestEvent::class)]
    public function handleRequest(HttpRequestEvent $requestEvent): void
    {
        $method = Method::tryFrom($_SERVER['REQUEST_METHOD']) ?? Method::GET;
        $request = new HttpRequest(
            method: $method,
            uri: $_SERVER['REQUEST_URI'] ?? '/',
            body: match($method) {
                Method::POST => $_POST,
                default => $_GET,
            }
        );
        Twix::getContainer()->register(Request::class, fn () => $request);
    }

    #[Handler(HttpControllerEvent::class)]
    public function handleController(HttpControllerEvent $httpControllerEvent): void
    {
        $router = Twix::getContainer()->get(Router::class);
        $request = Twix::getContainer()->get(Request::class);

        $response = $router->dispatch($request);
        Twix::getContainer()->register(Response::class, fn () => $response);
    }

    #[Handler(HttpResponseEvent::class)]
    public function handleResponse(HttpResponseEvent $responseEvent): void
    {
        //        $httpResponse = Twix::getContainer()->get(Response::class);
        //        $newHttpResponse = new HttpResponse(Status::HTTP_404, "DOWN FOR MAINTENANCE!!!");
        //        Twix::getContainer()->register(Response::class, fn () => $newHttpResponse);
    }

    #[Handler(HttpResponderEvent::class)]
    public function handleResponderEvent(HttpResponderEvent $httpResponderEvent): void
    {
        $httpResponder = Twix::getContainer()->get(HttpResponder::class);
        $httpResponse = Twix::getContainer()->get(Response::class);
        $httpResponder->send($httpResponse);
    }

    #[Handler(HttpErrorResponse::class)]
    public function handleErrorResponseEvent(HttpErrorResponse $httpErrorResponse): void
    {
        $container = Twix::getContainer();
        $currentResponse = $container->isRegistered(Response::class)
            ? $container->get(Response::class)
            : false;

        // will not run if there is a current successful Response
        // this allows previously generated Error responses to persist
        if (! $currentResponse || $currentResponse->getStatus()->isSuccessful()) {

            $httpErrorResponse = new HttpResponse(
                status: $httpErrorResponse->getHttpErrorStatus(),
                body: $httpErrorResponse->getThrowable()?->getMessage() ?? "Error",
                headers: []
            );

            $container->register(Response::class, fn () => $httpErrorResponse);
        }

    }

    #[Handler(HttpTerminationEvent::class)]
    public function handleHttpTermination(HttpTerminationEvent $terminationEvent): void
    {
        // handle post response stuff here
    }
}
