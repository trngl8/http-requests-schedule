<?php

namespace App\Tests\Unit;

use App\UserInput;
use PHPUnit\Framework\TestCase;
use App\Command;

class CommandTest extends TestCase
{
    public function testChoiceDefaultSuccess(): void
    {
        $userInput = $this->createMock(UserInput::class);
        $userInput->method('input')->willReturn('y');
        $target = new Command('app:test');
        $target->setUserInput($userInput);
        $result = $target->getChoice();
        $this->assertFalse($result);
    }

    public function testChoiceRandom(): void
    {
        $userInput = $this->createMock(UserInput::class);
        $userInput->method('input')->willReturn('sdfgsdfgdfg');
        $target = new Command('app:test');
        $target->setUserInput($userInput);
        $result = $target->getChoice();
        $this->assertFalse($result);
    }
}
