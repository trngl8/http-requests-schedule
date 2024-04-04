<?php

namespace App;

class Database
{
    private \SQLite3 $db;

    public function __construct(string $name)
    {
        $this->db = new \SQLite3(sprintf(__DIR__ . '/../var/%s.db', $name), SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $this->db->enableExceptions(true);
    }

    public function prepare(string $sql): \SQLite3Stmt
    {
        return $this->db->prepare($sql);
    }

    public function fetch(string $table, array $where): array
    {
        $condition = [];
        foreach ($where as $key => $value) {
            if (is_array($value)) {
                $condition[] = "$key IN ('" . implode("', '", $value) . "')";
            }
            if (is_null($value)) {
                $condition[] = "$key IS NULL";
            }
        }
        $conditions = implode(' AND ', $condition);
        $statement = $this->db->prepare("SELECT * FROM $table WHERE $conditions");
        $items = $statement->execute();

        $result = [];
        while ($item = $items->fetchArray(SQLITE3_ASSOC)) {
            $result[] = $item;
        }
        return $result;
    }

    public function insert(string $table, array $data): void
    {
        $fields = implode(', ', array_keys($data));
        $values = implode(', ', array_map(fn($v) => "'$v'", $data));
        $this->db->query("INSERT INTO $table ($fields) VALUES ($values)");
    }

    public function update(string $table, array $data, array $where): void
    {
        $set = implode(', ', array_map(fn($k, $v) => "$k = '$v'", array_keys($data), $data));
        $where = implode(' AND ', array_map(fn($k, $v) => "$k = '$v'", array_keys($where), $where));
        $this->db->query("UPDATE $table SET $set WHERE $where");
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
