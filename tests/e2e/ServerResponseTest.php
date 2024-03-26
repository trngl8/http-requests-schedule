<?php

namespace App\Tests\EndToEnd;

use App\DataRepository;
use App\HttpClient;
use App\HttpService;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class ServerResponseTest extends TestCase
{
    public function testServer404Success(): void
    {
        $repository = new DataRepository();
        $logger = new Logger('test');
        $logger->pushHandler(new StreamHandler('var/logs/http.log', Level::Info));
        $target = new HttpService(new HttpClient(), $logger, $repository);
        $target->get('http://localhost:8080');
        $result = $target->getResponse();
        $this->assertEquals(404, $result->statusCode);
    }

    public function testServerPostEmpty(): void
    {
        $repository = new DataRepository();
        $logger = new Logger('test');
        $logger->pushHandler(new StreamHandler('var/logs/http.log', Level::Info));
        $target = new HttpService(new HttpClient(), $logger, $repository);
        $target->post('http://localhost:8080', ['key' => 'value']);
        $result = $target->getResponse();
        $this->assertEquals(404, $result->statusCode);
    }
}
