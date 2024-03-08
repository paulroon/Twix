<?php

namespace App\Controllers;

use Exception;
use Twix\Application\AppConfig;
use Twix\Http\Get;
use Twix\Http\HttpResponse;
use Twix\Http\Post;
use Twix\Http\Status;
use Twix\Interfaces\Logger;
use Twix\Interfaces\Request;
use Twix\Interfaces\Response;

final readonly class DefaultController
{
    public function __construct(
        private Logger $logger,
        private AppConfig $appConfig,
        private readonly Request $request
    ) {
    }

    /**
     * @throws Exception
     */
    #[Get('/')]
    public function index(): Response
    {
        $env = $this->appConfig->getEnv();


        // throw new Exception('My Application Error!!');

        return new HttpResponse(Status::HTTP_200, sprintf('[%s] Homepage!', $env));
    }

    #[Post('/welcome/{message}')]
    #[Get('/welcome/{message}')]
    public function show(string $message): Response
    {
        $this->logger->critical('hello from the controller!!! - ');

        return new HttpResponse(Status::HTTP_200, sprintf('Hello %s! - URL[%s]', $message, $this->request->getUri()));
    }
}
