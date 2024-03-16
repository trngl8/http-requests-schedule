<?php

namespace App\tests\e2e;

use App\HttpClient;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class HttpClientTest extends TestCase
{
    public function testServerAnyUrl(): void
    {
        $target = new HttpClient($this->createMock(Logger::class));
        $target->get('any url');
        $this->assertTrue(true);
    }

    public function testServerPostAnyUrl(): void
    {
        $target = new HttpClient($this->createMock(Logger::class));
        $target->post('any url', ['key' => 'value']);
        $this->assertTrue(true);
    }
}
