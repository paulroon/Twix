<?php

namespace Twix\Interfaces;

interface Connection
{
    public function getConfig(): ConnectionConfig;

    public function setConfig(ConnectionConfig $config): self;
}
