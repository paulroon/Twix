<?php

namespace Twix;

use Dotenv\Dotenv;
use Twix\Application\AppConfig;
use Twix\Application\HttpApplication;
use Twix\Application\Kernel;
use Twix\Container\TwixContainer;
use Twix\Container\HTTPContainerInitializer;
use Twix\Events\TwixEventBus;
use Twix\Interfaces\Application;
use Twix\Interfaces\Container;
use Twix\Interfaces\EventBus;

final class Twix
{
    public static Kernel $kernel;

    public static function boot(string $rootDir): Twix
    {
        try {
            $dotenv = Dotenv::createUnsafeImmutable($rootDir);
            $dotenv->safeLoad();

            $container = new TwixContainer();
            $container
                ->singleton(Container::class, fn () => $container)
                ->singleton(AppConfig::class, fn () => new AppConfig(
                        twixRoot: realpath(__DIR__),
                        root: realpath($rootDir),
                        env: env('ENVIRONMENT', 'dev')
                    )
                )
                ->singleton(EventBus::class, fn () => new TwixEventBus());

            self::$kernel = new Kernel($container);

        } catch (\Throwable $e) {
            // handle
        }

        return new self();
    }

    public function http(): HttpApplication
    {
        $container = $this->getKernel()->init();

        $container->singleton(
            classname: Application::class,
            definition: fn () => new HttpApplication(HTTPContainerInitializer::init($container))
        );

        return $container->get(Application::class);
    }

    public static function getContainer(): Container
    {
        return self::$kernel->getContainer();
    }

    public static function getKernel(): Kernel
    {
        return self::$kernel;
    }

    public static function getAppConfig(): AppConfig
    {
        return self::$kernel->getContainer()->get(AppConfig::class);
    }
}