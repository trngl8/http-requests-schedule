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

    private $uris = [];

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
        $items = $this->db->fetch('requests', ['finished_at' => null]);
        $processed = $uris = [];
        foreach ($items as $item) {
            try {
                $result = $this->client->request($item['method'], $item['url']);

                if (empty($result)) {
                    throw new TransportException('Curl transport error');
                }
                //Transaction

                $this->db->exec('BEGIN');
                $this->db->update('requests', ['finished_at' => date('Y-m-d H:i:s')], ['id' => $item['id']]);
                $this->db->insert('responses', [
                    'request_id' => $item['id'],
                    'code' => $this->client->getStatusCode(),
                    'body' => htmlspecialchars($result),
                ]);
                $this->db->exec('COMMIT');
                $processed[] = $item;

                $doc = new \DOMDocument();
                $doc->loadHTML($result);
                $xpath = new \DOMXPath($doc);
                $nodes = $xpath->query('//a');
                foreach ($nodes as $node) {
                    $uris[] = $node->getAttribute('href');
                }
                $this->logger->info(sprintf('Request %d processed', $item['id']));
            } catch (ValidatorException $e) {
                $this->logger->warning($e->getMessage());
            } catch (TransportException $e) {
                $this->logger->error($e->getMessage());
            }
        }
        $this->processed = $processed;
        $this->uris = $uris;
    }

    public function getProcessedCount(): int
    {
        return count($this->processed);
    }

    public function getUris(): array
    {
        return $this->uris;
    }
}
