<?php

namespace App;

use App\Exception\TransportException;
use App\Exception\ValidatorException;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class QueueHandler
{
    private $logger;

    private $db;

    private $client;

    private $processed = [];

    public function __construct(Database $db = null, HttpClient $client = null, Logger $logger = null)
    {
        $transport = new CurlTransport();

        $this->logger = $logger ?: new Logger('test');
        $this->db = $db ?: new Database('requests');
        $this->client = $client ?: new HttpClient($transport);
    }

    public function setLogger(Logger $logger): self
    {
        $this->logger = $logger->pushHandler(new StreamHandler(__DIR__ . '../../var/logs/http-queue.log', Level::Info));
        return $this;
    }

    public function run(): void
    {
        $items = $this->db->fetch('requests', ['finished_at' => null], );
        $processed = [];
        foreach ($items as $item) {
            try {
                $result = $this->client->request($item['method'], $item['url']);

                $this->db->exec('BEGIN');
                $this->db->update('requests', ['finished_at' => date('Y-m-d H:i:s')], ['id' => $item['id']]);
                $this->db->insert('responses', [
                    'request_id' => $item['id'],
                    'code' => $this->client->getStatusCode(),
                    'body' => htmlspecialchars($result),
                ]);
                $this->db->exec('COMMIT');
                $this->logger->info(sprintf('Request %d processed', $item['id']));
                $processed[] = $item;
            } catch (ValidatorException $e) {
                $this->logger->warning($e->getMessage());
            } catch (TransportException $e) {
                $this->logger->error($e->getMessage());
            }
        }
        $this->processed = $processed;
    }

    public function getProcessedCount(): int
    {
        return count($this->processed);
    }
}
