<?php

namespace App;

use App\Exception\DatabaseException;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class Database
{
    private \SQLite3 $db;

    private Logger $logger;

    private SQLQueryInterface $sqlQuery;

    public function __construct(string $name)
    {
        $path = sprintf(__DIR__ . '/../var/%s', $name);

        if (!file_exists($path)) {
            $this->db = new \SQLite3($path, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        } else {
            $this->db = new \SQLite3($path, SQLITE3_OPEN_READWRITE);
        }

        $this->db->enableExceptions(true);
        $this->sqlQuery = new SQLQuery();
        $this->logger = new Logger('database');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../var/logs/database.log', Level::Info));
    }

    /**
     * @throws DatabaseException
     */
    public function fetch(string $table, array $where): array
    {
        $sql = $this->sqlQuery->query($table, $where);
        $statement = $this->db->prepare($sql);

        if ($statement === false) {
            $this->logger->error($sql);
            $this->logger->error($this->db->lastErrorMsg());
            throw new DatabaseException($this->db->lastErrorMsg());
        }

        $items = $statement->execute();

        $result = [];
        while ($item = $items->fetchArray(SQLITE3_ASSOC)) {
            $result[] = $item;
        }

        if (empty($result)) {
            $this->logger->warning(sprintf("No records found for %s", $sql));
        }
        return $result;
    }

    /**
     * @throws DatabaseException
     */
    public function insert(string $table, array $data): void
    {
        $sql = $this->sqlQuery->insert($table, $data);

        try {
            $result = $this->db->query($sql);
        } catch (\Exception $e) {
            $this->logger->error($sql);
            $this->logger->error($this->db->lastErrorMsg());
            throw new DatabaseException($e->getMessage());
        }

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
            $this->logger->error($sql);
            $this->logger->error($this->db->lastErrorMsg());
            throw new DatabaseException($this->db->lastErrorMsg());
        }

        $this->logger->info($sql);
        $this->logger->info(sprintf("%d columns in result", $result->numColumns()));
    }

    /**
     * @throws DatabaseException
     */
    public function exec(string $sql): void
    {
        $result = $this->db->exec($sql);

        if ($result === false) {
            $this->logger->error($sql);
            $this->logger->error($this->db->lastErrorMsg());
            throw new DatabaseException($this->db->lastErrorMsg());
        }
    }

    public function lastInsertRowID(): int
    {
        return $this->db->lastInsertRowID();
    }
}
