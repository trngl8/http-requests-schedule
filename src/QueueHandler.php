<?php

namespace App;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class QueueHandler
{
    private $logger;

    public function __construct()
    {
        $this->logger = new Logger('test');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../var/logs/http.log', Level::Info));
    }

    public function run(): void
    {
        $db = new \SQLite3(__DIR__ . '/../var/requests.table.db', SQLITE3_OPEN_READWRITE);
        $db->enableExceptions(true);

        $statement = $db->prepare('SELECT * FROM requests WHERE finished_at IS NULL');
        $items = $statement->execute();

        while ($item = $items->fetchArray(SQLITE3_ASSOC)) {
            $transport = new CurlTransport();
            $client = new HttpClient($transport);
            try {
                $result = $client->request($item['method'], $item['url']);

                $db->exec('BEGIN');
                $db->query(sprintf('UPDATE requests SET finished_at = "%s" WHERE id = %d', date('Y-m-d H:i:s'), $item['id']));
                $db->query(sprintf('INSERT INTO responses(request_id, code, body) VALUES (%d, %d, "%s")', $item['id'], $client->getStatusCode(), htmlspecialchars($result)));
                $db->exec('COMMIT');
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
