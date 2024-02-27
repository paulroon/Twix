<?php

namespace Twix\Http;

use Twix\Interfaces\Response;

final readonly class HttpResponder
{
    public function send(Response $response): Response
    {
        ob_start();

        $response = $this->prepareResponse($response);

        $this->sendHeaders($response);
        $this->sendContent($response);

        ob_end_flush();

        return $response;
    }

    private function sendHeaders(Response $response): void
    {
        if (headers_sent()) {
            return;
        }

        foreach ($response->getHeaders() as $key => $value) {
            header("{$key}: {$value}");
        }

        http_response_code((int) $response->getStatus()->value);
    }

    private function sendContent(Response $response): void
    {
        echo $response->getBody();
    }

    private function prepareResponse(Response $response): Response
    {
        $body = $response->getBody();

        if (is_array($body)) {
            $response->addHeader('Content-Type', 'application/json');
            $response->body(json_encode($body));
        }

        return $response;
    }
}