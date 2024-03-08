<?php

declare(strict_types=1);

namespace Twix {


    function env(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        return match (strtolower($value)) {
            'true' => true,
            'false' => false,
            'null', '' => null,
            default => $value,
        };
    }


    function runHttpApp(string $appRoot): void
    {
        try {
            $application = Twix::boot($appRoot)->http();
            $application->run();
        } catch (\Throwable $throwable) {
            $application->handleError($throwable);
        }

    }
}
