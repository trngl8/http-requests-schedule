<?php

namespace App\Tests\Unit;

use App\Database;
use App\Exception\ValidatorException;
use App\QueueHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class QueueHandlerTest extends TestCase
{
    public function testValidatorException(): void
    {
        $database = $this->createMock(Database::class);
        $target = new QueueHandler(new Logger('test'), $database);
        $database->method('fetch')->willReturn([
            ['id' => 1, 'method' => 'NONE', 'url' => 'http://localhost'],
            ['id' => 2, 'method' => 'GET', 'url' => '///']
        ]);
        $target->run();
        $this->assertTrue(true);
    }

    public function testSomething(): void
    {
        $database = $this->createMock(Database::class);
        $target = new QueueHandler(new Logger('test'), $database);
        $database->method('fetch')->willReturn([
            ['id' => 1, 'method' => 'GET', 'url' => 'http://localhost'],
            ['id' => 2, 'method' => 'POST', 'url' => 'http://localhost']
        ]);
        $target->run();
        $this->assertTrue(true);
    }
}
