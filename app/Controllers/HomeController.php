<?php

namespace App\Controllers;

use Twix\Http\Get;
use Twix\Http\HttpResponse;
use Twix\Http\Status;
use Twix\Interfaces\Response;

final readonly class HomeController
{
    #[Get('/home')]
    public function index(): Response
    {
        return new HttpResponse(Status::HTTP_200, "Hello World!");
    }

    #[Get('/greet/{name}')]
    public function show(string $name): Response
    {
        return new HttpResponse(Status::HTTP_200, sprintf("Hello %s!", $name));
    }

    #[Get('/greet/{name}/with/{thing}')]
    public function show2(string $name, string $thing): Response
    {
        return new HttpResponse(Status::HTTP_200, sprintf("Hello %s, heres a %s!", $name, $thing));
    }
}