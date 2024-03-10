<?php

namespace Twix\Filesystem;

use DateTime;
use Twix\Application\AppConfig;
use Twix\Interfaces\Writer;

final class FileWriter implements Writer
{
    private int $lastWriteBytes = 0;

    private string $fileName;

    public function __construct(
        private readonly AppConfig $appConfig
    ) {
        $logFilePath = sprintf(
            '%s%svar%slogs%s%s_%s.log',
            $this->appConfig->getRoot(),
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            $this->appConfig->getEnv(),
            (new DateTime())->format('Y_W')
        );
        $this->fileName = $logFilePath;
    }



    public function write(string $text): int
    {
        if (! is_dir(dirname($this->fileName))) {
            mkdir(dirname($this->fileName), 0755, true);
        }

        $this->lastWriteBytes = file_put_contents($this->fileName, $text) ?? 0;

        return $this->getLastWriteBytes();
    }

    public function append(string $text): int
    {
        if (! is_dir(dirname($this->fileName))) {
            return $this->write($text);
        }

        $this->lastWriteBytes = file_put_contents($this->fileName, $text, FILE_APPEND) ?? 0;

        return $this->getLastWriteBytes();
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getLastWriteBytes(): int
    {
        return $this->lastWriteBytes;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;
        return $this;
    }
}
