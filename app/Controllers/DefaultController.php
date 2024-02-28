<?php

namespace App\Controllers;

use Twix\Application\AppConfig;
use Twix\Http\Get;
use Twix\Http\HttpResponse;
use Twix\Http\Status;
use Twix\Interfaces\Response;
use Twix\Twix;

final readonly class DefaultController
{
    #[Get('/')]
    public function index(): Response
    {
        $env = Twix::getContainer()->get(AppConfig::class)->getEnv();
        return new HttpResponse(Status::HTTP_200, sprintf("[%s] Homepage!", $env));
    }

    #[Get('/say/{message}')]
    public function show(string $message): Response
    {
        return new HttpResponse(Status::HTTP_200, sprintf("Hello %s!", $message));
    }

}