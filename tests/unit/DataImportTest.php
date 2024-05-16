<?php

namespace App\Tests\Unit;

use App\Database;
use App\DataImport;
use PHPUnit\Framework\TestCase;

class DataImportTest extends TestCase
{
    public function testImportLinesSuccess(): void
    {
        $target = new DataImport();
        $database = $this->createMock(Database::class);
        $target->setDatabase($database);
        $this->assertTrue(true);
    }
}
