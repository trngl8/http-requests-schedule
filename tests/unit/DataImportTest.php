<?php

namespace App\Tests\Unit;

use App\Database;
use App\DataImport;
use App\Exception\DatabaseException;
use PHPUnit\Framework\TestCase;

class DataImportTest extends TestCase
{
    public function testImportLinesError(): void
    {
        $target = new DataImport();
        $database = $this->createMock(Database::class);
        $database->method('insert')->willThrowException(new DatabaseException('error'));
        $target->setDatabase($database);
        $target->processLines('requests', ['any,data']);
        $this->assertCount(1, $target->getErrors());
    }

    public function testImportLinesSuccess(): void
    {
        $target = new DataImport();
        $database = $this->createMock(Database::class);
        $target->setDatabase($database);
        $target->processLines('requests', ['any,data']);
        $this->assertCount(0, $target->getErrors());
    }
}
