<?php

namespace App\Clients;

use Twix\Application\Boot;
use Twix\Http\HttpConnection;
use Twix\Http\HttpConnectionConfig;

#[Boot('Boot')]
readonly class JsonPlaceHolderClient extends HttpConnection
{
    public static function Boot(): self
    {
        $httpConnectionConfig = new HttpConnectionConfig([
            'url' => 'https://jsonplaceholder.typicode.com',
        ]);

        return (new self())->setConfig($httpConnectionConfig);
    }
}
