<?php

namespace App\Tests\Integration;

use App\Database;
use App\DataImport;
use App\Exception\DatabaseException;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private $db;

    public function setUp(): void
    {
        $name = 'test';
        $dbPath = sprintf(__DIR__ . '/../../var/%s.db', $name);
        if (file_exists($dbPath)) {
            unlink($dbPath);
        }
        $this->db = new \SQLite3($dbPath, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $initTablesSql = file_get_contents(__DIR__ . '/../../database/init_sqlite.sql');
        $parts = explode(';', $initTablesSql);
        foreach ($parts as $part) {
            $this->db->query($part);
        }
    }

    public function testDatabaseExec(): void
    {
        $target = new Database('test');
        $target->exec('DROP TABLE IF EXISTS not_exists');
        $this->assertTrue(true);
    }

    public function testDatabase(): void
    {
        $target = new Database('test');
        $result = $target->fetch('requests',  []);
        $this->assertEmpty($result);
    }

    public function testDatabaseInsertException(): void
    {
        $target = new Database('test');
        $this->expectException(DatabaseException::class);
        $target->insert('requests',  ['id' => 1]);
    }

    public function testDatabaseInsertSuccess(): void
    {
        $target = new Database('test');
        $target->insert('requests',  ['method' => 'GET', 'url' => 'http://example.com']);
        $ID = $target->lastInsertRowID();
        $this->assertEquals(1, $ID);

        $result = $target->fetch('requests',  ['id' => 1]);
        $this->assertCount(1, $result);
    }

    public function testDatabaseUpdateSuccess(): void
    {
        $target = new Database('test');
        $target->insert('requests',  ['method' => 'GET', 'url' => 'http://example.com']);
        $target->update('requests',  ['method' => 'POST', 'url' => 'http://example.com'], ['id' => 1]);
        $rec = $target->fetch('requests', ['id' => 1]);
        $this->assertCount(1, $rec);
        $this->assertEquals('POST', $rec[0]['method']);
    }

    public function testImportLinesSuccess(): void
    {
        $target = new DataImport();
        $database = new Database('test');
        $target->setDatabase($database);

        $result = $target->processLines(['GET,http://example.com']);

        $this->assertCount(1, $target->getErrors());
        $this->assertEquals(0, $result);
    }
}
