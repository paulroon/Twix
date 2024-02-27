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
        //here
        return new HttpResponse(Status::HTTP_200, "Hello World!");
    }
}