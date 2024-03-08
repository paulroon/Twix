<?php

namespace App;

use Twix\Application\AppConfig;
use Twix\Events\ApplicationBootEvent;
use Twix\Events\Handler;
use Twix\Events\HttpErrorResponse;
use Twix\Events\LogEvent;
use Twix\Filesystem\FileWriter;
use Twix\Interfaces\Logger;
use Twix\Twix;

final readonly class Bootstrap
{
    public function __construct(
        private readonly AppConfig $appConfig,
        private readonly Logger $logger
    ) {
    }

    #[Handler(ApplicationBootEvent::class)]
    public function bootstrap(): void
    {
        $logFilePath = 'var' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $this->appConfig->getEnv() . '.log';
        Twix::getContainer()->register(
            FileWriter::class,
            fn () => new FileWriter($this->appConfig->getRoot(), $logFilePath)
        );
        $this->logger->debug('Bootstrapping Application');

    }

    #[Handler(HttpErrorResponse::class)]
    public function other(): void
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

        Twix::getContainer()->get(FileWriter::class)->append($message . PHP_EOL);
    }
}
