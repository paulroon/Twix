<?php

namespace App\Controllers;

use Twix\Http\Get;
use Twix\Http\HttpResponse;
use Twix\Http\Status;
use Twix\Interfaces\Response;

final readonly class DefaultController
{
    #[Get('/')]
    public function index(): Response
    {
        return new HttpResponse(Status::HTTP_200, "Homepage!");
    }

    #[Get('/say/{message}')]
    public function show(string $message): Response
    {
        return new HttpResponse(Status::HTTP_200, sprintf("Hello %s!", $message));
    }

}