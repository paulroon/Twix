<?php

namespace Twix\Test;

use PHPUnit\Framework\MockObject\MockObject;
use Twix\Http\HttpConnection;
use Twix\Http\HttpConnectionConfig;

class ConnectionTest extends TestCase
{
    public const URL = 'https://jsonplaceholder.typicode.com';

    private HttpConnection $connection;
    private MockObject $configMock;

    public function setup(): void
    {
        parent::setup();
        $httpConnectionConfig = new HttpConnectionConfig([
            'url' => self::URL,
        ]);

        $this->configMock = $this->getMockBuilder(HttpConnectionConfig::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->configMock
            ->expects($this->any())
            ->method('getUrl')
            ->willReturn(self::URL);

        $this->connection = new HttpConnection($httpConnectionConfig);
    }

    /** @test */
    public function testGetConfig()
    {
        $configuredUrl = $this->connection->getConfig()->getUrl();
        $this->assertEquals(self::URL, $configuredUrl);
    }
}
