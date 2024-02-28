<?php

namespace Twix;

use Dotenv\Dotenv;
use Twix\Application\AppConfig;
use Twix\Application\HttpApplication;
use Twix\Application\Kernel;
use Twix\Container\GenericContainer;
use Twix\Container\HTTPContainerInitializer;
use Twix\Interfaces\Application;
use Twix\Interfaces\Container;

final readonly class Twix
{
    private function __construct(
        private Kernel $kernel
    ) { }

    public static function boot(string $rootDir): Twix
    {
        try {
            $dotenv = Dotenv::createUnsafeImmutable($rootDir);
            $dotenv->safeLoad();

            $container = new GenericContainer();
            $container
                ->singleton(Container::class, fn () => $container)
                ->singleton(AppConfig::class, fn () => new AppConfig(
                        root: realpath($rootDir),
                        env: env('ENVIRONMENT', 'dev')
                    )
                );

            return new self(kernel: new Kernel($container));

        } catch (\Throwable $e) {
            // handle
        }
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

    public function getContainer(): Container
    {
        return $this->getKernel()->getContainer();
    }

    public function getKernel(): Kernel
    {
        return $this->kernel;
    }

    public function getAppConfig(): AppConfig
    {
        return $this->getContainer()->get(AppConfig::class);
    }
}