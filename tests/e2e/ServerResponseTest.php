<?php

namespace App\Tests\EndToEnd;

use App\CurlTransport;
use App\DataRepository;
use App\HttpClient;
use App\HttpService;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class ServerResponseTest extends TestCase
{
    /**
     * This tests runs on local server only
     */
     const LOCAL_HOST = 'http://localhost:8080';

    public function testServer404Success(): void
    {
        $repository = new DataRepository();
        $transport = new CurlTransport();
        $logger = new Logger('test');
        $logger->pushHandler(new StreamHandler('var/logs/http.log', Level::Info));
        $target = new HttpService(new HttpClient($transport), $logger, $repository);
        $target->get(self::LOCAL_HOST);
        $result = $target->getResponse();
        $this->assertEquals(200, $result->statusCode);
    }

    public function testServerPostEmpty(): void
    {
        $repository = new DataRepository();
        $transport = new CurlTransport();
        $logger = new Logger('test');
        $logger->pushHandler(new StreamHandler('var/logs/http.log', Level::Info));
        $target = new HttpService(new HttpClient($transport), $logger, $repository);
        $target->post(self::LOCAL_HOST, ['key' => 'value']);
        $result = $target->getResponse();
        $this->assertEquals(200, $result->statusCode);
    }

    public function testServerRun(): void
    {
        $repository = new DataRepository();
        $transport = new CurlTransport();
        $logger = new Logger('test');
        $logger->pushHandler(new StreamHandler('var/logs/http.log', Level::Info));
        $target = new HttpService(new HttpClient($transport), $logger, $repository);
        //$target->post(self::LOCAL_HOST.'/run', []);
        $result = $target->getResponse();
        $this->assertEquals(200, $result->statusCode);
    }
}
