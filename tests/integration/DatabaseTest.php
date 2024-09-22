<?php

namespace App\Tests\Integration;

use App\Database;
use App\DataImport;
use App\Exception\DatabaseException;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    const DB_PATH = __DIR__ . '/../../var/';

    const DB_NAME = 'localhost.db';

    private $db;

    public function setUp(): void
    {
        $dbPath = self::DB_PATH . DIRECTORY_SEPARATOR . self::DB_NAME;
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
        $target = new Database(self::DB_NAME);
        $target->exec('DROP TABLE IF EXISTS not_exists');
        $this->assertTrue(true);
    }

    public function testDatabase(): void
    {
        $target = new Database(self::DB_NAME);
        $result = $target->fetch('requests',  []);
        $this->assertEmpty($result);
    }

    public function testDatabaseInsertException(): void
    {
        $target = new Database(self::DB_NAME);
        $this->expectException(DatabaseException::class);
        $target->insert('requests',  ['id' => 1]);
    }

    public function testDatabaseInsertSuccess(): void
    {
        $target = new Database(self::DB_NAME);
        $target->insert('requests',  ['method' => 'GET', 'url' => 'http://example.com']);
        $ID = $target->lastInsertRowID();
        $this->assertEquals(1, $ID);

        $result = $target->fetch('requests',  ['id' => 1]);
        $this->assertCount(1, $result);
    }

    public function testDatabaseUpdateSuccess(): void
    {
        $target = new Database(self::DB_NAME);
        $target->insert('requests',  ['method' => 'GET', 'url' => 'http://example.com']);
        $target->update('requests',  ['method' => 'POST', 'url' => 'http://example.com'], ['id' => 1]);
        $rec = $target->fetch('requests', ['id' => 1]);
        $this->assertCount(1, $rec);
        $this->assertEquals('POST', $rec[0]['method']);
    }

    public function testImportLinesError(): void
    {
        $target = new DataImport();
        $database = new Database(self::DB_NAME);
        $target->setDatabase($database);

        //process without headers
        $result = $target->processLines('requests', ['GET,http://example.com']);

        $this->assertCount(1, $target->getErrors());
        $this->assertEquals(0, $result);
    }

    public function testImportLinesSuccess(): void
    {
        $target = new DataImport();
        $database = new Database(self::DB_NAME);
        $target->setDatabase($database);
        $target->initHeaders('requests', ['method', 'url']);
        $result = $target->processLines('requests', ['GET,http://example.com']);

        $this->assertCount(0, $target->getErrors());
        $this->assertEquals(1, $result);
    }
}
