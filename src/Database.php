<?php

namespace App;

class Database
{
    private \SQLite3 $db;

    private SQLQueryInterface $sqlQuery;

    public function __construct(string $name)
    {
        $this->db = new \SQLite3(sprintf(__DIR__ . '/../var/%s.db', $name), SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $this->db->enableExceptions(true);
        $this->sqlQuery = new SQLQuery();
    }

    public function fetch(string $table, array $where): array
    {
        $sql = $this->sqlQuery->query($table, $where);
        $statement = $this->db->prepare($sql);
        $items = $statement->execute();

        $result = [];
        while ($item = $items->fetchArray(SQLITE3_ASSOC)) {
            $result[] = $item;
        }
        return $result;
    }

    public function insert(string $table, array $data): void
    {
        $sql = $this->sqlQuery->insert($table, $data);
        $this->db->query($sql);
    }

    public function update(string $table, array $data, array $where): void
    {
        $sql = $this->sqlQuery->update($table, $data, $where);
        $this->db->query($sql);
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
