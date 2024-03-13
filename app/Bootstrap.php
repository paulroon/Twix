<?php

namespace App;

use Twix\Application\AppConfig;
use Twix\Events\ApplicationBootEvent;
use Twix\Events\Handler;
use Twix\Events\HttpErrorResponseEvent;
use Twix\Events\LogEvent;
use Twix\Filesystem\FileWriter;
use Twix\Http\HttpResponse;
use Twix\Http\Status;
use Twix\Interfaces\Logger;
use Twix\Interfaces\Response;
use Twix\Twix;

final readonly class Bootstrap
{
    public function __construct(
        private AppConfig  $appConfig,
        private Logger     $logger,
        private FileWriter $fileWriter
    ) {
    }

    #[Handler(ApplicationBootEvent::class)]
    public function bootstrap(): void
    {
        $container = Twix::getContainer();
        $container
            ->singleton(
                FileWriter::class,
                fn () => new FileWriter($this->appConfig)
            );
        $this->logger->debug('Bootstrapping Application');

    }

    #[Handler(HttpErrorResponseEvent::class)]
    public function customErrorResponseHandler(HttpErrorResponseEvent $httpErrorResponse): void
    {
        $throwable = $httpErrorResponse->getThrowable();

        $httpErrorResponse = new HttpResponse(
            status: Status::HTTP_404,
            body: 'Custom:: ' . $throwable->getMessage(),
            headers: []
        );

        Twix::getContainer()->register(Response::class, fn () => $httpErrorResponse);
    }

    #[Handler(LogEvent::class)]
    public function logHandler(LogEvent $logEvent): void
    {
        $message = sprintf(
            '%s%s%s%s%s',
            $logEvent->getTime() . ' ',
            $this->appConfig->getThreadId() . ' ',
            str_pad($logEvent->getLogLevel(), 11, ' ', STR_PAD_RIGHT),
            str_pad($logEvent->getMessage(), 70, ' ', STR_PAD_RIGHT),
            str_pad($logEvent->getUuid(), 33, ' ', STR_PAD_RIGHT)
        );

        $this->fileWriter->append($message . PHP_EOL);
    }
}
