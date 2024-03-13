<?php

namespace App;

use App\Clients\JsonPlaceHolderClient;
use App\Events\CustomEvent;
use Exception;
use Twix\Application\AppConfig;
use Twix\Events\Handler;
use Twix\Http\Get;
use Twix\Http\HttpResponse;
use Twix\Http\Post;
use Twix\Http\Status;
use Twix\Interfaces\Logger;
use Twix\Interfaces\Request;
use Twix\Interfaces\Response;

final readonly class MyApplication
{
    public function __construct(
        private Logger $logger,
        private AppConfig $appConfig,
        private JsonPlaceHolderClient $jsonPlaceHolderClient,
        private Request $request
    ) {
    }

    /**
     * @throws Exception
     */
    #[Get('/')]
    public function index(): Response
    {
        $env = $this->appConfig->getEnv();

        //
        //        throw new Exception('My Application Error!!');

        return new HttpResponse(Status::HTTP_200, sprintf('[%s] Homepage!', $env));
    }

    #[Post('/welcome/{message}')]
    #[Get('/welcome/{message}')]
    public function show(string $message): Response
    {
        $this->logger->critical('hello from the controller!!! - ');

        return new HttpResponse(Status::HTTP_200, sprintf('Hello %s! - URL[%s]', $message, $this->request->getUri()));
    }

    #[Get('/todos')]
    public function todos(): Response
    {
        $jsonResponse = $this->jsonPlaceHolderClient->getJson('/todos');
        $message = implode(
            '</li><li>',
            array_map(fn (array $todo) => sprintf('User:: %s - [%s]', $todo['userId'], $todo['title']), $jsonResponse)
        );

        return new HttpResponse(Status::HTTP_200, sprintf('<ul><li>%s</li></ul>', $message));
    }

    #[Handler(CustomEvent::class)]
    public function customEventHandler(): void
    {
        dump('handling CustomHandlers::customEventHandler()');
    }
}
