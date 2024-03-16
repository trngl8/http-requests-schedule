<?php

namespace App\tests\e2e;

use App\HttpClient;
use PHPUnit\Framework\TestCase;

class HttpClientTest extends TestCase
{
    public function testServer404Success(): void
    {
        $target = new HttpClient();
        $target->get('http://localhost:8080');
        $result = $target->getResponse();
        $this->assertEquals(404, $result->statusCode);
    }
}
