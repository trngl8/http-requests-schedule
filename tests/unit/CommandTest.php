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
        $userInput->method('getArgs')->willReturn([]);
        $target = new Command('app:test');
        $target->setUserInput($userInput);
        $target->choice(true);
        $result = $target->getChoice();
        $this->assertTrue($result);
    }
}
