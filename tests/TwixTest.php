<?php

namespace Twix\Test;

use PHPUnit\Framework\TestCase;
use Twix\Application\AppConfig;
use Twix\Twix;

class TwixTest extends TestCase
{
    /** @test */
    public function testBoot()
    {
        $app = Twix::boot('../');

        $this->assertSame($app->getAppConfig()::class, AppConfig::class);
    }
}
