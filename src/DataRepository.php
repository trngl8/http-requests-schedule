<?php

namespace App;

class DataRepository
{
    public function save(string $method, string $url, int $code, string $body = ''): void
    {
        $db = new Database('requests');

        $db->exec('BEGIN');
        $db->insert('requests', [
            'method' => $method,
            'url' => $url,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $db->insert('responses', [
            'request_id' => $db->lastInsertRowID(),
            'code' => $code,
            'body' => $body,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $db->exec('COMMIT');
    }
}
