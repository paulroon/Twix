<?php

namespace Twix\Logger;

use Ramsey\Uuid\Uuid;

final class LogItem
{
    private readonly string $uuid;
    private int $time;

    public function __construct(
        private readonly LogLevel $level,
        private readonly string   $message
    ) {
        $this->uuid = Uuid::uuid4()->toString();
        $this->time = time();
    }

    public function getLevel(): LogLevel
    {
        return $this->level;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function setTime(int $time): void
    {
        $this->time = $time;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
}
