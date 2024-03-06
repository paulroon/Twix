<?php

namespace Twix\Events;

use Twix\Http\Status;
use Twix\Interfaces\Event;

final readonly class HttpErrorResponse implements Event
{
    public function __construct(private \Throwable $throwable, private Status $status = Status::HTTP_500)
    {
    }

    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }

    public function getHttpErrorStatus(): Status
    {
        return $this->status;
    }
}
