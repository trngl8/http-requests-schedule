<?php

namespace App\Tests\Unit;

use App\Exception\{TransportException, ValidatorException};
use App\HttpClient;
use App\TransportInterface;
use PHPUnit\Framework\TestCase;

class HttpClientTest extends TestCase
{
    public function testHttpClientInvalidURL(): void
    {
        $transport = $this->createMock(TransportInterface::class);
        $target = new HttpClient($transport);
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessageMatches('/^URL is not allowed: (.+)$/');
        $target->request('GET', 'localhost');
    }

    public function testHttpClientInvalidMethod(): void
    {
        $transport = $this->createMock(TransportInterface::class);
        $target = new HttpClient($transport);
        $this->expectException(ValidatorException::class);
        $this->expectExceptionMessageMatches('/^Method is not allowed: (.+)$/');
        $target->request('ANY', 'http://localhost');
    }

    public function testHttpClientTransportException(): void
    {
        $transport = $this->createMock(TransportInterface::class);
        $transport->method('execute')->willReturn('');
        $target = new HttpClient($transport);
        $this->expectException(TransportException::class);
        $this->expectExceptionMessageMatches('/^Curl transport error (.+)$/');
        $target->request('GET', 'http://localhost:8080');
    }

    public function testHttpClientGetSuccess(): void
    {
        $transport = $this->createMock(TransportInterface::class);
        $transport->method('execute')->willReturn('any response');
        $transport->method('getInfo')->willReturn(200);
        $target = new HttpClient($transport);
        $target->request('GET', 'http://localhost:8080');
        $this->assertEquals(200, $target->getStatusCode());
    }

    public function testHttpClientPostSuccess(): void
    {
        $transport = $this->createMock(TransportInterface::class);
        $transport->method('execute')->willReturn('any response');
        $transport->method('getInfo')->willReturn(200);
        $target = new HttpClient($transport);
        $target->request('POST', 'http://localhost:8080', ['data' => 'value']);
        $this->assertEquals(200, $target->getStatusCode());
    }
}
