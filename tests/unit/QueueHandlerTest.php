<?php

namespace App\Tests\Unit;

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
