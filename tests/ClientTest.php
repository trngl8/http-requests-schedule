<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

use App\HttpClient;

class ClientTest extends TestCase
{
    public function testServer404Success(): void
    {
        $target = new HttpClient();
        $target->get('http://localhost:8080');
        $result = $target->getResponse();
        $this->assertEquals(404, $result->statusCode);
        $this->assertEquals(533, strlen($result->content));
    }
}
