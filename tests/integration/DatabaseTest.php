<?php

namespace App\Tests\Integration;

use App\Database;
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

    public function testDatabase(): void
    {
        $target = new Database('test');
        $result = $target->fetch('requests',  ['id' => null]);
        $this->assertEmpty($result);
    }
}