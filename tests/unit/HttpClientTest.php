<?php

namespace App\tests\e2e;

use App\HttpClient;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testServer404Success(): void
    {
        $target = new HttpClient();
        $target->get('any url');
        $this->assertTrue(true);
    }
}
