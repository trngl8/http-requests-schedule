<?php

namespace App\Tests\Integration;

use App\QueueHandler;
use PHPUnit\Framework\TestCase;

class QueueHandlerTest extends TestCase
{
    public function testSomething(): void
    {
        $target = new QueueHandler();
        $target->run();
        $this->assertTrue(true);
    }
}
