<?php

namespace Twix\Test\Logger;

use Twix\Exceptions\ContainerException;
use Twix\Interfaces\Container;
use Twix\Interfaces\Logger;
use Twix\Logger\LogItem;
use Twix\Logger\LogLevel;
use Twix\Logger\TwixLogger;
use Twix\Test\TestCase;

class TwixLoggerTest extends TestCase
{
    /**
     * @throws ContainerException
     */
    public function setup(): void
    {
        parent::setup();

        $this->container->singleton(
            Logger::class,
            fn (Container $container) => new TwixLogger()
        );
    }

    /**
     * @throws ContainerException
     */
    public function testLoggerFetchFromContainer()
    {
        $logger = $this->container->get(Logger::class);
        $this->assertInstanceOf(TwixLogger::class, $logger);
    }

    public function testLogFnFromString()
    {
        /** @var TwixLogger $logger */
        $logger = $this->container->get(Logger::class);
        $logger->log("I am a message");

        $stack = $logger->getLogStack();
        $stackValues = array_values($stack); // they are assoc indexed with timestamps - zero this out

        $this->assertCount(1, $stack);

        $logItem = $stackValues[0];
        $this->assertEquals("I am a message", $logItem->getMessage());
        $this->assertEquals(LogLevel::INFO, $logItem->getLevel()); // default
    }

    /**
     * @throws ContainerException
     */
    public function testFromLogItem()
    {
        /** @var TwixLogger $logger */
        $logger = $this->container->get(Logger::class);

        $logItem = new LogItem(LogLevel::INFO, "Pre-Constructed");
        $logger->log($logItem);

        $stack = $logger->getLogStack();
        $stackValues = array_values($stack); // they are assoc indexed with timestamps - zero this out

        $this->assertCount(1, $stack);

        $logItem = $stackValues[0];
        $this->assertEquals("Pre-Constructed", $logItem->getMessage());
        $this->assertEquals(LogLevel::INFO, $logItem->getLevel()); // default
    }

    /**
     * @return array
     */
    public static function logLevelDataProvider(): array
    {
        return [
            [LogLevel::DEBUG, 'debug'],
            [LogLevel::INFO, 'info'],
            [LogLevel::NOTICE, 'notice'],
            [LogLevel::WARNING, 'warning'],
            [LogLevel::ERROR, 'error'],
            [LogLevel::CRITICAL, 'critical'],
            [LogLevel::ALERT, 'alert'],
            [LogLevel::EMERGENCY, 'emergency']
        ];
    }


    /**
     * @dataProvider logLevelDataProvider
     * @throws ContainerException
     */
    public function testLevelledLogMethods($logLevel, $methodName)
    {
        /** @var TwixLogger $logger */
        $logger = $this->container->get(Logger::class);

        $logger->$methodName("$methodName Message");

        $stack = $logger->getLogStack();
        $stackValues = array_values($stack); // they are assoc indexed with timestamps - zero this out

        $this->assertCount(1, $stack);

        $logItem = $stackValues[0];

        $this->assertEquals("$methodName Message", $logItem->getMessage());
        $this->assertEquals($logLevel, $logItem->getLevel()); // default
    }

    /**
     * @throws ContainerException
     */
    public function testStack()
    {
        /** @var TwixLogger $logger */
        $logger = $this->container->get(Logger::class);

        $logger->debug("debug Message");

        $this->assertCount(1, $logger->getLogStack());

        $logger->info("info Message");
        $logger->emergency("emergency Message");
        $logger->alert("alert Message");

        $this->assertCount(4, $logger->getLogStack());

    }


}
