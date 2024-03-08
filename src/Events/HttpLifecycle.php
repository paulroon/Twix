<?php

namespace Twix\Events;

use Twix\Http\HttpRequest;
use Twix\Http\HttpResponder;
use Twix\Http\HttpResponse;
use Twix\Http\Method;
use Twix\Http\Status;
use Twix\Interfaces\Logger;
use Twix\Interfaces\Request;
use Twix\Interfaces\Response;
use Twix\Interfaces\Router;
use Twix\Twix;

final readonly class HttpLifecycle
{
    public function __construct(
        private readonly Logger $logger
    ) {
    }

    #[Handler(ApplicationBootEvent::class)]
    public function handleApplicationBoot(ApplicationBootEvent $applicationBootEvent): void
    {
        // leave this for the App to handle
    }

    #[Handler(HttpRequestEvent::class)]
    public function handleRequest(HttpRequestEvent $requestEvent): void
    {
        $this->logger->debug(__METHOD__ . '[HttpRequestEvent]');

        $method = Method::tryFrom($_SERVER['REQUEST_METHOD']) ?? Method::GET;

        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))))] = $value;
            }
        }

        $request = new HttpRequest(
            method: $method,
            uri: $_SERVER['REQUEST_URI'] ?? '/',
            body: match($method) {
                Method::POST => file_get_contents('php://input'),
                default => '',
            },
            headers: $headers
        );

        Twix::getContainer()->register(Request::class, fn () => $request);

        $this->logger->info($request->describe());
    }

    #[Handler(HttpControllerEvent::class)]
    public function handleController(HttpControllerEvent $httpControllerEvent): void
    {
        $this->logger->debug(__METHOD__ . '[HttpControllerEvent]');

        $router = Twix::getContainer()->get(Router::class);
        $request = Twix::getContainer()->get(Request::class);

        $response = $router->dispatch($request);
        Twix::getContainer()->register(Response::class, fn () => $response);
    }

    #[Handler(HttpResponseEvent::class)]
    public function handleResponse(HttpResponseEvent $responseEvent): void
    {
        $this->logger->debug(__METHOD__ . '[HttpResponseEvent]');

        //        $httpResponse = Twix::getContainer()->get(Response::class);
        //        $newHttpResponse = new HttpResponse(Status::HTTP_404, "DOWN FOR MAINTENANCE!!!");
        //        Twix::getContainer()->register(Response::class, fn () => $newHttpResponse);
    }

    #[Handler(HttpResponderEvent::class)]
    public function handleResponderEvent(HttpResponderEvent $httpResponderEvent): void
    {
        $this->logger->debug(__METHOD__ . '[HttpResponderEvent]');

        $httpResponder = Twix::getContainer()->get(HttpResponder::class);
        $httpResponse = Twix::getContainer()->get(Response::class);
        $httpResponder->send($httpResponse);
    }

    #[Handler(HttpErrorResponseEvent::class)]
    public function handleErrorResponseEvent(HttpErrorResponseEvent $httpErrorResponse): void
    {
        $this->logger->debug(__METHOD__ . '[HttpErrorResponse]');

        $container = Twix::getContainer();
        $currentResponse = $container->isRegistered(Response::class)
            ? $container->get(Response::class)
            : false;

        // will not run if there is a current successful Response
        // this allows previously generated Error responses to persist
        if (! $currentResponse || $currentResponse->getStatus()->isSuccessful()) {

            $httpErrorResponse = new HttpResponse(
                status: $httpErrorResponse->getHttpErrorStatus(),
                body: $httpErrorResponse->getThrowable()?->getMessage() ?? 'Error',
                headers: []
            );

            $container->register(Response::class, fn () => $httpErrorResponse);
        }

    }

    #[Handler(HttpTerminationEvent::class)]
    public function handleHttpTermination(HttpTerminationEvent $terminationEvent): void
    {
        // $this->>logger->debug(__METHOD__ . '[HttpTerminationEvent]');
    }
}
