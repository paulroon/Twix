<?php

namespace Twix\Events;

use Twix\Interfaces\Event;
use Twix\Logger\LogItem;

final readonly class LogEvent implements Event
{
    public function __construct(
        private string $logLevel,
        private string $message,
        private int $time,
        private string $uuid
    ) {
    }

    public function getLogLevel(): string
    {
        return $this->logLevel;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public static function From(LogItem $logItem): self
    {
        return new self(
            $logItem->getLevel()->value,
            $logItem->getMessage(),
            $logItem->getTime(),
            $logItem->getUuid()
        );
    }
}
