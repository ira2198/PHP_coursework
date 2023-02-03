<?php

namespace UnitTests;

use PHPUnit\Framework\TestCase;

class HelloTest extends TestCase
{
    public function testItWorks(): void
    {
        // Проверяем, что true – это true
        $this->assertTrue(true);
        $this->assertTrue(true);
    }

    public function testAdd(): void
{
    $this->assertEquals(4, 2+2);
}
}
