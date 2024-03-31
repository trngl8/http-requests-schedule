<?php

namespace App;

class DataRepository
{
    public function save(string $method, string $url, int $code, string $body = ''): void
    {
        $db = new \SQLite3(__DIR__ . '/../var/requests.table.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
        $db->enableExceptions(true);

        $db->exec('BEGIN');
        $db->query(sprintf('INSERT INTO requests(method, url, created_at) VALUES ("%s", "%s", "%s")', $url, $method, date('Y-m-d H:i:s')));
        $db->query(sprintf('INSERT INTO responses(request_id, code, body, created_at) VALUES (%d, %d, "%s", "%s")', $db->lastInsertRowID(), $code, $body, date('Y-m-d H:i:s')));
        $db->exec('COMMIT');
    }
}
