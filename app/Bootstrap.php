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
    #[Handler(ApplicationBootEvent::class)]
    public function bootstrap(): void
    {

        $appConfig = Twix::getContainer()->get(AppConfig::class);
        $logFilePath = 'var' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $appConfig->getEnv() . '.log';
        Twix::getContainer()->register(
            FileWriter::class,
            fn () => new FileWriter($appConfig->getRoot(), $logFilePath)
        );
        Twix::getContainer()->get(Logger::class)->debug('Bootstrapping Application');

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
            '%s%s%s%s',
            str_pad($logEvent->getTime(), 15, ' ', STR_PAD_RIGHT),
            str_pad($logEvent->getLogLevel(), 10, ' ', STR_PAD_RIGHT),
            str_pad($logEvent->getMessage(), 70, ' ', STR_PAD_RIGHT),
            str_pad($logEvent->getUuid(), 32, ' ', STR_PAD_RIGHT)
        );

        Twix::getContainer()->get(FileWriter::class)->append($message . PHP_EOL);
    }
}
