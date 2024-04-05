<?php

namespace App;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class QueueHandler
{
    private $logger;

    private $db;

    public function __construct()
    {
        $this->logger = new Logger('test');
        $this->db = new Database('requests');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../var/logs/http.log', Level::Info));
    }

    public function run(): void
    {
        $items = $this->db->fetch('requests', ['finished_at' => null], );

        foreach ($items as $item) {
            $transport = new CurlTransport();
            $client = new HttpClient($transport);
            try {
                $result = $client->request($item['method'], $item['url']);

                $this->db->exec('BEGIN');
                $this->db->update('requests', ['finished_at' => date('Y-m-d H:i:s')], ['id' => $item['id']]);
                $this->db->insert('responses', [
                    'request_id' => $item['id'],
                    'code' => $client->getStatusCode(),
                    'body' => htmlspecialchars($result),
                ]);
                $this->db->exec('COMMIT');

            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
