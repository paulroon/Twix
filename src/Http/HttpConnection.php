<?php

namespace Twix\Http;

use Exception;
use Twix\Interfaces\Connection;

readonly class HttpConnection implements Connection
{
    private string $lastResponse;

    public function __construct(private HttpConnectionConfig $config)
    {
    }

    /**
     * @throws \JsonException
     * @throws Exception
     */
    public function getJson(string $urlPath, $headers = []): array
    {
        return json_decode(
            json: $this->get($urlPath, $headers),
            associative: true,
            flags: JSON_THROW_ON_ERROR
        );
    }

    /**
     * @throws Exception
     */
    public function get(string $urlPath, $headers = []): string
    {
        $url = $this->config->getUrl() . $urlPath;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $this->lastResponse = curl_exec($ch);

        if ($this->lastResponse === false) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);

        return (string) $this->lastResponse;
    }

    /**
     * @throws Exception
     */
    public function post($url, $data = [], $headers = []): string
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $this->lastResponse = curl_exec($ch);

        if ($this->lastResponse === false) {
            throw new Exception(curl_error($ch));
        }

        curl_close($ch);

        return (string) $this->lastResponse;
    }

    public function getConfig(): HttpConnectionConfig
    {
        return $this->config;
    }
}
