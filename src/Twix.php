<?php

namespace Twix;

use Dotenv\Dotenv;
use Ramsey\Uuid\Uuid;
use Twix\Application\AppConfig;
use Twix\Application\HttpApplication;
use Twix\Application\Kernel;
use Twix\Application\MinimalApplication;
use Twix\Application\TwixClassRegistry;
use Twix\Container\TwixContainer;
use Twix\Events\TwixEventBus;
use Twix\Interfaces\Application;
use Twix\Interfaces\ClassRegistry;
use Twix\Interfaces\Container;
use Twix\Interfaces\EventBus;
use Twix\Interfaces\Logger;
use Twix\Logger\TwixLogger;

final class Twix
{
    public static Kernel $kernel;

    public static function boot(string $rootDir, array $options = [ 'env' => 'dev', 'appDir' => 'app' ]): Twix
    {
        try {

            $dotenv = Dotenv::createUnsafeImmutable($rootDir);
            $dotenv->safeLoad();

            $container = new TwixContainer();
            $container
                ->singleton(Container::class, fn () => $container)
                ->singleton(
                    AppConfig::class,
                    fn () => new AppConfig(
                        twixRoot: realpath(__DIR__),
                        root: realpath($rootDir),
                        env: env('ENVIRONMENT', $options['env']),
                        appDir: env('APPDIR', $options['appDir']),
                        threadId: Uuid::uuid4()->toString()
                    )
                )
                ->singleton(ClassRegistry::class, fn () => new TwixClassRegistry())
                ->singleton(EventBus::class, fn (Container $c) => new TwixEventBus($c))
                ->singleton(Logger::class, fn (Container $c) => new TwixLogger($c->get(EventBus::class)));

            self::$kernel = new Kernel($container);

        } catch (\Throwable $e) {
            // Fatal Error during Boot sequence
            echo 'Twix Boot Error:: could not load';
            exit;
        }

        return new self();
    }

    public function http(): HttpApplication
    {
        $container = $this->getKernel()->init();

        $container->singleton(
            classname: Application::class,
            definition: fn () => new HttpApplication($container, $container->get(EventBus::class))
        );

        return $container->get(Application::class);
    }

    public function minimal(): MinimalApplication
    {
        $container = $this->getKernel()->init();

        $container->singleton(
            classname: Application::class,
            definition: fn () => new MinimalApplication($container, $container->get(EventBus::class))
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
