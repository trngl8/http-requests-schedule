<?php

namespace App\Tests\EndToEnd;

use App\CurlTransport;
use App\HttpClient;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class ServerResponseTest extends TestCase
{
    public function testServer404Success(): void
    {
        $logger = new Logger('test');
        $logger->pushHandler(new StreamHandler('var/logs/http.log', Level::Info));
        $target = new HttpClient(new CurlTransport(), $logger);
        $target->get('http://localhost:8080');
        $result = $target->getResponse();
        $this->assertEquals(404, $result->statusCode);
    }

    public function testServerPostEmpty(): void
    {
        $logger = new Logger('test');
        $logger->pushHandler(new StreamHandler('var/logs/http.log', Level::Info));
        $target = new HttpClient(new CurlTransport(), $logger);
        $target->post('http://localhost:8080', ['key' => 'value']);
        $result = $target->getResponse();
        $this->assertEquals(404, $result->statusCode);
    }
}
