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
            'var%slogs%s%s_%s.log',
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            $this->appConfig->getEnv(),
            (new DateTime())->format('Y_W')
        );
        $this->fileName = $logFilePath;
    }

    public function write(string $text): int
    {
        $fPath = $this->getFullPath();

        if (! is_dir(dirname($fPath))) {
            mkdir(dirname($fPath), 0755, true);
        }

        $this->lastWriteBytes = file_put_contents($fPath, $text) ?? 0;

        return $this->getLastWriteBytes();
    }

    public function append(string $text): int
    {
        if (! is_dir(dirname($this->getFullPath()))) {
            return $this->write($text);
        }
        $this->lastWriteBytes = file_put_contents($this->getFullPath(), $text, FILE_APPEND) ?? 0;

        return $this->getLastWriteBytes();
    }

    public function getFullPath(): string
    {
        return sprintf('%s%s%s', $this->appConfig->getRoot(), DIRECTORY_SEPARATOR, $this->fileName);
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getLastWriteBytes(): int
    {
        return $this->lastWriteBytes;
    }
}
