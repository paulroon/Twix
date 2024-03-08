<?php

namespace Twix\Logger;

use Twix\Events\LogEvent;
use Twix\Interfaces\EventBus;
use Twix\Interfaces\Logger;

final class TwixLogger implements Logger
{
    private LogLevel $defaultLevel;

    /** @var array|LogItem[] */
    private array $logStack = [];

    public function __construct(
        private readonly EventBus $eventBus,
    ) {
        $this->defaultLevel = LogLevel::INFO;
    }

    public function log(string|LogItem $message): void
    {
        $logItem = ($message instanceof LogItem)
            ? $message
            : new LogItem($this->defaultLevel, $message);


        $this->eventBus->dispatch(LogEvent::From($logItem));
        $this->logStack[$logItem->getUuid()] = $logItem;
    }

    public function debug(string $message): void
    {
        $this->log(new LogItem(LogLevel::DEBUG, $message));
    }

    public function info(string $message): void
    {
        $this->log(new LogItem(LogLevel::INFO, $message));
    }

    public function notice(string $message): void
    {
        $this->log(new LogItem(LogLevel::NOTICE, $message));
    }

    public function warning(string $message): void
    {
        $this->log(new LogItem(LogLevel::WARNING, $message));
    }

    public function error(string $message): void
    {
        $this->log(new LogItem(LogLevel::ERROR, $message));
    }

    public function critical(string $message): void
    {
        $this->log(new LogItem(LogLevel::CRITICAL, $message));
    }

    public function alert(string $message): void
    {
        $this->log(new LogItem(LogLevel::ALERT, $message));
    }

    public function emergency(string $message): void
    {
        $this->log(new LogItem(LogLevel::EMERGENCY, $message));
    }

    public function getDefaultLevel(): LogLevel
    {
        return $this->defaultLevel;
    }

    /**
     * @return array|LogItem[]
     */
    public function getLogStack(): array
    {
        return $this->logStack;
    }

    public function getLog(): array
    {
        return array_map(fn (LogItem $logItem) => [
            $logItem->getLevel()->value,
            $logItem->getTime(),
            $logItem->getMessage(),
        ], $this->getLogStack());
    }
}
