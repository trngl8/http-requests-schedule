<?php

namespace App\Tests\Unit;

use App\Database;
use App\HttpClient;
use App\QueueHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class QueueHandlerTest extends TestCase
{
    public function testSomething(): void
    {
        $database = $this->createMock(Database::class);
        $client = $this->createMock(HttpClient::class);
        $logger = $this->createMock(Logger::class);
        $database->method('fetch')->willReturn([
            ['id' => 1, 'method' => 'GET', 'url' => 'http://localhost'],
            ['id' => 2, 'method' => 'PST', 'url' => 'http://localhost'],
            ['id' => 3, 'method' => 'NONE', 'url' => 'http://localhost'],
            ['id' => 4, 'method' => 'GET', 'url' => '///']
        ]);

        $target = new QueueHandler($database, $client);
        $target->setLogger($logger);
        $target->run();

        $this->assertEquals(4, $target->getProcessedCount());
    }
}
