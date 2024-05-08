<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{
    public function testCommandSuccess(): void
    {
        $target = new Command('app:test', 'localhost:8080');
        $result = $target->execute();
        $this->assertTrue($result);
    }
}
