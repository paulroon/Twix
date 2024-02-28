<?php

namespace Twix\Test\Filesystem;

use PHPUnit\Framework\TestCase;
use Twix\Filesystem\ClassInspector;

class ClassInspectorTest extends TestCase
{
    public function testMethodAttributeInspection()
    {
        $hasAttribute = ClassInspector::HasMethodWithAttribute(WithMethodAttr::class);
        $this->assertTrue($hasAttribute);
    }

    public function testNoMethodAttributeInspection()
    {
        $hasAttribute = ClassInspector::HasMethodWithAttribute(NoMethodAttr::class);
        $this->assertFalse($hasAttribute);
    }
}

#[\Attribute]
class TestAttr {}

class WithMethodAttr {
    #[TestAttr]
    public function thisOne() {}
}

class NoMethodAttr {
    public function thisOne() {}
}
