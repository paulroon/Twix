<?php

declare(strict_types=1);

namespace Twix {


    use Twix\Interfaces\Application;

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
        $twix = Twix::boot($appRoot);

        $application = null;

        try {
            $application = $twix->http();
            $application->run();
        } catch (\Throwable $throwable) {
            if ($application instanceof Application) {
                dd($throwable);
                die('Application did not load.');
            }
            $application->handleError($throwable);
        }

    }
}
