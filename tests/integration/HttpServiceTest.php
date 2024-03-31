<?php

namespace App\Tests\Integration;

use App\DataRepository;
use App\HttpClientInterface;
use App\HttpService;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class HttpServiceTest extends TestCase
{
    public function testServerAnyUrl(): void
    {
        $transport = $this->createMock(HttpClientInterface::class);
        $logger = $this->createMock(Logger::class);
        $repository = $this->createMock(DataRepository::class);
        $transport->method('request')->willReturn('any response');
        $target = new HttpService($transport, $logger, $repository);
        $target->get('any url');
        $this->assertTrue(true);
    }

    public function testServerPostAnyUrl(): void
    {
        $transport = $this->createMock(HttpClientInterface::class);
        $logger = $this->createMock(Logger::class);
        $repository = $this->createMock(DataRepository::class);
        $target = new HttpService($transport, $logger, $repository);
        $target->post('any url', ['key' => 'value']);
        $this->assertTrue(true);
    }
}
