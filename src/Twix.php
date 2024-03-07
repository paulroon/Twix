<?php

namespace Twix;

use Dotenv\Dotenv;
use ReflectionClass;
use ReflectionException;
use Twix\Application\AppConfig;
use Twix\Application\Boot;
use Twix\Application\HttpApplication;
use Twix\Application\Kernel;
use Twix\Container\HTTPContainerInitializer;
use Twix\Container\TwixContainer;
use Twix\Events\TwixEventBus;
use Twix\Filesystem\ClassFinder;
use Twix\Filesystem\ClassInspector;
use Twix\Interfaces\Application;
use Twix\Interfaces\Container;
use Twix\Interfaces\EventBus;
use Twix\Interfaces\Logger;
use Twix\Logger\TwixLogger;

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
                ->singleton(
                    AppConfig::class,
                    fn () => new AppConfig(
                        twixRoot: realpath(__DIR__),
                        root: realpath($rootDir),
                        env: env('ENVIRONMENT', 'dev')
                    )
                )
                ->singleton(EventBus::class, fn () => new TwixEventBus())
                ->singleton(Logger::class, fn () => new TwixLogger());

            self::bootstrapApplication(realpath($rootDir), $container);

            self::$kernel = new Kernel($container);

        } catch (\Throwable $e) {
            // handle
        }

        return new self();
    }

    /**
     * @throws ReflectionException
     */
    private static function bootstrapApplication(string $rootpath, Container &$container): void
    {
        $appClasses = ClassFinder::findClassesInDir($rootpath . '/app');

        $bootstrapClasses = array_filter(
            $appClasses,
            fn (string $controllerClass) => ClassInspector::HasMethodWithAttribute($controllerClass, [
                Boot::class,
            ])
        );

        foreach ($bootstrapClasses as $bootClass) {
            $reflectionClass = new ReflectionClass($bootClass);
            foreach ($reflectionClass->getMethods() as $reflectionMethod) {
                $reflectionHandlerAttributes = $reflectionMethod->getAttributes(Boot::class);

                foreach($reflectionHandlerAttributes as $attribute) {
                    if ($attribute->getName() === Boot::class) {
                        $bootClass = $container->get($reflectionMethod->getDeclaringClass()->getName());
                        $reflectionMethod->invoke($bootClass);
                    }
                }

            }
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
