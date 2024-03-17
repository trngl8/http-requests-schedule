<?php

namespace App\Tests\Unit;

use App\HttpClient;
use App\HttpTransportInterface;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class HttpClientTest extends TestCase
{
    public function testServerAnyUrl(): void
    {
        $transport = $this->createMock(HttpTransportInterface::class);
        $logger = $this->createMock(Logger::class);
        $transport->method('get')->willReturn('any response');
        $target = new HttpClient($transport, $logger);
        $target->get('any url');
        $this->assertTrue(true);
    }

    public function testServerPostAnyUrl(): void
    {
        $transport = $this->createMock(HttpTransportInterface::class);
        $logger = $this->createMock(Logger::class);
        $target = new HttpClient($transport, $logger);
        $target->post('any url', ['key' => 'value']);
        $this->assertTrue(true);
    }
}
