<?php

namespace Twix\Filesystem;

use Twix\Interfaces\Writer;

final class FileWriter implements Writer
{
    private int $lastWriteBytes = 0;
    private readonly string $path;

    public function __construct(
        string $path,
        private readonly string $fileName
    ) {
        $this->path = realpath($path);
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

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFullPath(): string
    {
        return sprintf('%s%s%s', $this->path, DIRECTORY_SEPARATOR, $this->fileName);
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
