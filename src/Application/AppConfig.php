<?php

namespace Twix\Application;

final readonly class AppConfig
{
    public function __construct(
        private string $twixRoot,
        private string $root,
        private string $env,
        private string $appDir,
        private string $threadId,
    ) {
    }

    public function getTwixRoot(): string
    {
        return $this->twixRoot;
    }

    public function getEnv(): string
    {
        return $this->env;
    }

    public function getRoot(): string
    {
        return realpath($this->root);
    }

    public function getAppDir(): string
    {
        return $this->appDir;
    }

    public function getThreadId(): string
    {
        return $this->threadId;
    }

    public function getAppPath(): string
    {
        return sprintf(
            '%s/%s',
            $this->getRoot(),
            $this->getAppDir()
        );
    }
}
