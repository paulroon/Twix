<?php

namespace Twix\Test\Http\Fixtures;

use Twix\Http\Get;
use Twix\Http\HttpResponse;
use Twix\Http\Status;
use Twix\Interfaces\Response;

final readonly class TestController
{
    #[Get('/')]
    public function index(): Response
    {
        return new HttpResponse(Status::HTTP_200, 'Hello World!');
    }

    #[Get('/greet/{name}')]
    public function show(string $name): Response
    {
        return new HttpResponse(Status::HTTP_200, sprintf('Hello %s!', $name));
    }

    #[Get('/greet/{name}/with/{thing}')]
    public function show2(string $name, string $thing): Response
    {
        return new HttpResponse(Status::HTTP_200, sprintf("Hello %s, here's a %s!", $name, $thing));
    }
}
