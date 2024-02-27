<?php

namespace App\Controllers;

use Twix\Http\Get;
use Twix\Http\HttpResponse;
use Twix\Http\Status;
use Twix\Interfaces\Response;

final readonly class BlogController
{
    #[Get('/blog/home')]
    public function index(): Response
    {
        return new HttpResponse(Status::HTTP_200, "Blog Home");
    }
}