<?php

namespace App\Tests\Integration;

use App\Database;
use App\HttpClient;
use App\QueueHandler;
use PHPUnit\Framework\TestCase;

class QueueHandlerTest extends TestCase
{
    public function testSomething(): void
    {
        $database = $this->createMock(Database::class);
        $client = $this->createMock(HttpClient::class);

        $target = new QueueHandler($database, $client);
        $target->run();
        $this->assertTrue(true);
    }
}
