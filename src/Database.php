<?php

namespace App;

use App\Exception\DatabaseException;

class Database
{
    private \SQLite3 $db;

    private SQLQueryInterface $sqlQuery;

    public function __construct(string $name)
    {
        $this->db = new \SQLite3(sprintf(__DIR__ . '/../var/%s.db', $name), SQLITE3_OPEN_READWRITE);
        $this->db->enableExceptions(true);
        $this->sqlQuery = new SQLQuery();
    }

    /**
     * @throws DatabaseException
     */
    public function fetch(string $table, array $where): array
    {
        $sql = $this->sqlQuery->query($table, $where);
        $statement = $this->db->prepare($sql);

        if ($statement === false) {
            throw new DatabaseException($this->db->lastErrorMsg());
        }

        $items = $statement->execute();

        $result = [];
        while ($item = $items->fetchArray(SQLITE3_ASSOC)) {
            $result[] = $item;
        }
        return $result;
    }

    /**
     * @throws DatabaseException
     */
    public function insert(string $table, array $data): void
    {
        $sql = $this->sqlQuery->insert($table, $data);

        $result = $this->db->query($sql);

        if ($result === false) {
            throw new DatabaseException($this->db->lastErrorMsg());
        }
    }

    /**
     * @throws DatabaseException
     */
    public function update(string $table, array $data, array $where): void
    {
        $sql = $this->sqlQuery->update($table, $data, $where);
        $result = $this->db->query($sql);

        if ($result === false) {
            throw new DatabaseException($this->db->lastErrorMsg());
        }
    }

    /**
     * @throws DatabaseException
     */
    public function exec(string $sql): void
    {
        $result = $this->db->exec($sql);

        if ($result === false) {
            throw new DatabaseException($this->db->lastErrorMsg());
        }
    }

    public function lastInsertRowID(): int
    {
        return $this->db->lastInsertRowID();
    }
}
