<?php

namespace Twix\Interfaces;

use Twix\Http\Status;

interface Response
{
    public function getStatus(): Status;

    public function getBody(): string;
}