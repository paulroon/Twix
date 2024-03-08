<?php

namespace App;

use Twix\Application\AppConfig;
use Twix\Events\ApplicationBootEvent;
use Twix\Events\Handler;
use Twix\Events\HttpErrorResponseEvent;
use Twix\Events\LogEvent;
use Twix\Filesystem\FileWriter;
use Twix\Interfaces\Logger;
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
        Twix::getContainer()->singleton(
            FileWriter::class,
            fn () => new FileWriter($this->appConfig)
        );
        $this->logger->debug('Bootstrapping Application');

    }

    #[Handler(HttpErrorResponseEvent::class)]
    public function other(HttpErrorResponseEvent $errorResponse): void
    {
        dump(debug_backtrace());
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
