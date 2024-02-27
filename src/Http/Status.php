<?php

namespace Twix\Http;

enum Status: string
{
    case HTTP_200 = "200";
    case HTTP_201 = "201";
    case HTTP_404 = "404";
    case HTTP_500 = "500";
}
