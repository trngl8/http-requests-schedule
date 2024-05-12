<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Command;

class CommandTest extends TestCase
{
    public function testChoiceDefaultSuccess(): void
    {
        $target = new Command('app:test', ['localhost:8080']);
        $target->choice(true);
        $result = $target->getChoice();
        $this->assertTrue($result);
    }
}
