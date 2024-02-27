<?php

use Twix\Twix;

require_once __DIR__ . '/../vendor/autoload.php';

$twix = Twix::boot(__DIR__ . '/../');

$httpApp = $twix->http();

//    $router = $twix->getContainer()->get(\Twix\Interfaces\Router::class);
//    dd($router->listControllers());

$httpApp->run();

exit;