<?php

namespace App\Tests\Unit;

use App\Database;
use App\HttpClient;
use App\QueueHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class QueueHandlerTest extends TestCase
{
    public function testSomethingInvalid(): void
    {
        $database = $this->createMock(Database::class);
        $client = $this->createMock(HttpClient::class);
        $client->method('request')->willReturn('');
        $logger = $this->createMock(Logger::class);
        $database->method('fetch')->willReturn([
            ['id' => 3, 'method' => 'NONE', 'url' => 'http://localhost'],
            ['id' => 4, 'method' => 'GET', 'url' => '///']
        ]);

        $target = new QueueHandler($database, $client);
        $target->setLogger($logger);
        $target->run();

        $this->assertEquals(0, $target->getProcessedCount());
        $this->assertCount(2, $target->getUris());
    }

    public function testSomethingSuccess(): void
    {
        $database = $this->createMock(Database::class);
        $client = $this->createMock(HttpClient::class);
        $client->method('request')->willReturn('
            <html>
                <head>
                    <title>Test</title>
                </head>
                <body>
                    <h1>Test</h1>
                    <a href="http://localhost">Test</a>
                </body>
            </html>');
        $logger = $this->createMock(Logger::class);
        $database->method('fetch')->willReturn([
            ['id' => 1, 'method' => 'GET', 'url' => 'http://localhost'],
            ['id' => 2, 'method' => 'POST', 'url' => 'http://localhost'],
        ]);

        $target = new QueueHandler($database, $client);
        $target->setLogger($logger);
        $target->run();

        $this->assertEquals(2, $target->getProcessedCount());
        $this->assertCount(4, $target->getUris());
    }
}
