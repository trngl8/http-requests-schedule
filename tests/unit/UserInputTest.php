<?php

namespace App\Tests\Unit;

use App\UserInput;
use PHPUnit\Framework\TestCase;

class UserInputTest extends TestCase
{
    public function testArgsSuccess(): void
    {
        $target = new UserInput();
        $this->assertGreaterThan(0, count($target->getArgs()));
    }
}