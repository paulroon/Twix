<?php

namespace App\Controllers;

use Exception;
use Twix\Application\AppConfig;
use Twix\Http\Get;
use Twix\Http\HttpResponse;
use Twix\Http\Status;
use Twix\Interfaces\Logger;
use Twix\Interfaces\Response;
use Twix\Twix;

final readonly class DefaultController
{
    /**
     * @throws Exception
     */
    #[Get('/')]
    public function index(): Response
    {
        $env = Twix::getContainer()->get(AppConfig::class)->getEnv();

        Twix::getContainer()->get(Logger::class)->critical("hello from the controller");

        //        throw new Exception('My Application Error');

        return new HttpResponse(Status::HTTP_200, sprintf("[%s] Homepage!", $env));
    }

    #[Get('/welcome/{message}')]
    public function show(string $message): Response
    {
        return new HttpResponse(Status::HTTP_200, sprintf("Hello %s!", $message));
    }
}
