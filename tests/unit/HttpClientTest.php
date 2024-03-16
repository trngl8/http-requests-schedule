<?php

namespace App\tests\e2e;

use App\HttpClient;
use PHPUnit\Framework\TestCase;

class HttpClientTest extends TestCase
{
    public function testServerAnyUrl(): void
    {
        $target = new HttpClient();
        $target->get('any url');
        $this->assertTrue(true);
    }
}
