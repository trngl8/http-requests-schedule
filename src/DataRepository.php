<?php

namespace App;

class DataRepository
{
    public function save(string $method, string $url, int $code): void
    {
        $db = new \SQLite3(__DIR__ . '/../var/requests.table.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $db->enableExceptions(true);

        $db->exec('BEGIN');
        $db->query(sprintf('INSERT INTO requests(method, url, code, created_at) VALUES ("%s", "%s", %d, "%s")',
        $url, $method, $code, date('Y-m-d H:i:s')));
        $db->exec('COMMIT');
    }
}
