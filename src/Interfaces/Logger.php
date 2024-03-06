<?php

namespace Twix\Interfaces;

interface Logger
{
    public function log(string $message): void;
    public function debug(string $message): void;
    public function info(string $message): void;
    public function notice(string $message): void;
    public function warning(string $message): void;
    public function error(string $message): void;
    public function critical(string $message): void;
    public function alert(string $message): void;
    public function emergency(string $message): void;
}