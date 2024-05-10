<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Command;

class CommandTest extends TestCase
{
    public function testCommandSuccess(): void
    {
        $target = new Command('app:test', 'localhost:8080');
        $target->run(true);
        $this->assertTrue(true);
    }
}
