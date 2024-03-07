<?php

declare(strict_types=1);

namespace Twix {

    use Twix\Exceptions\ContainerException;

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


    /**
     * @throws ContainerException
     */
    function runHttpApp(string $appRoot): void
    {
        $application = Twix::boot($appRoot)->http();

        try {
            $application->run();
        } catch (\Throwable $throwable) {
            $application->handleError($throwable);
        }

    }
}
