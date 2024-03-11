<?php

namespace Twix\Test;

use Twix\Application\AppConfig;

class TwixTest extends TestCase
{
    /** @test */
    public function testBoot()
    {
        $config = $this->get(AppConfig::class);
        $this->assertSame('test', $config->getEnv());
    }
}
