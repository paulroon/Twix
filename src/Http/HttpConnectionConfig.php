<?php

namespace Twix\Http;

use Twix\Interfaces\ConnectionConfig;

class HttpConnectionConfig implements ConnectionConfig
{
    private string $url;

    public function __construct(array $configParams)
    {
        $this->url = $configParams['url'] ?? '';
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
