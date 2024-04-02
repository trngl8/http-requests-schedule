<?php

namespace App;

class Database
{
    private \SQLite3 $db;

    public function __construct(string $name)
    {
        $this->db = new \SQLite3(sprintf(__DIR__ . '/../var/%s.table.db', $name), SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $this->db->enableExceptions(true);
    }

    public function insert(string $table, array $data): void
    {
        $fields = implode(', ', array_keys($data));
        $values = implode(', ', array_map(fn($v) => "'$v'", $data));
        $this->db->query("INSERT INTO $table ($fields) VALUES ($values)");
    }

    public function exec(string $sql): void
    {
        $this->db->exec($sql);
    }

    public function lastInsertRowID(): int
    {
        return $this->db->lastInsertRowID();
    }
}
