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

    private $items = [];

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

    public function init(): void
    {
        $this->items = $this->db->fetch('requests', ['finished_at' => null]);
        foreach ($this->items as $item) {
            $this->uris[] = $item['url'];
        }
    }

    public function setUris(array $uris): void
    {
        $this->uris = $uris;
    }

    public function run(): void
    {
        $this->init();
        $processed = $uris = [];
        foreach ($this->items as $item) {
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

    public function getNext()
    {
        return array_pop($this->uris);
    }

    public function getItems(): array
    {
        return $this->uris;
    }

    public function process(string $uri)
    {
        try {
            $result = $this->client->request('GET', $uri);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(). ' ' . $uri);
        }


        if (empty($result)) {
            throw new TransportException('Curl transport error');
        }

        $doc = new \DOMDocument();
        $doc->loadHTML($result);
        $xpath = new \DOMXPath($doc);
        $nodes = $xpath->query('//a');
        foreach ($nodes as $node) {
            $this->uris[] = $node->getAttribute('href');
        }
    }

    public function import(string $filename): void
    {
        if (!file_exists($filename)) {
            throw new \Exception(sprintf('File %s not found', $filename));
        }
        $data = file_get_contents($filename);
        $lines = explode("\n", $data);
        $first = array_shift($lines);
        $headers = explode(',', $first);
        if (count($headers) !== 1) {
            throw new \Exception('Invalid file format');
        }

        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }
            $parts = explode(',', $line);
            $item = array_combine($headers, $parts);
            $defaultData = [
                'method' => 'GET',
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->insert('requests', array_merge($defaultData, $item));
        }
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
