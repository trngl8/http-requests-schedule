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

    public function __construct(?Logger $logger = null, ?Database $db = null)
    {
        $this->logger = $logger ?:
            (new Logger('test'))
                ->pushHandler(new StreamHandler(__DIR__ . '../../var/logs/http.log', Level::Info))
        ;
        $this->db = $db ?: new Database('requests');
        $transport = new CurlTransport();
        $this->client = new HttpClient($transport);
    }

    public function run(): void
    {
        $items = $this->db->fetch('requests', ['finished_at' => null], );

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
            } catch (ValidatorException $e) {
                $this->logger->warning($e->getMessage());
            } catch (TransportException $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
