<?php

namespace App\Tests\Unit;

use App\UserInput;
use PHPUnit\Framework\TestCase;

class UserInputTest extends TestCase
{
    public function testArgsSuccess(): void
    {
        $target = new UserInput(false, []);

        $this->assertFalse($target->getForce());
        $this->assertCount(0, $target->getArgs());
    }
}
