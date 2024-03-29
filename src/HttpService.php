<?php

namespace App;

use App\Exception\TransportException;
use Monolog\Logger;

class HttpService
{
    private ResponseData $response;

    private HttpClientInterface $client;

    private DataRepository $repository;

    private $logger;

    public function __construct(HttpClientInterface $client, Logger $logger, DataRepository $repository)
    {
        $this->response = new ResponseData(404, '404 Not Found');
        $this->logger = $logger;
        $this->client = $client;
        $this->repository = $repository;
    }

    public function get(string $url): void
    {
        $result = $this->client->request('GET', $url);
        $this->response = new ResponseData($this->client->getStatusCode(), $result);
        $this->logger->info(sprintf('GET %s : %d', $url, $this->client->getStatusCode()));
        $this->repository->save($url, 'GET', $this->client->getStatusCode());
    }

    public function post(string $url, array $data): void
    {
        $result = $this->client->request('POST', $url, $data);
        $this->response = new ResponseData($this->client->getStatusCode(), $result);
        $this->logger->info(sprintf('POST %s : %d', $url, $this->client->getStatusCode()), $data);
        $this->repository->save($url, 'POST', $this->client->getStatusCode());
    }

    public function request(string $method, string $url, array $data): void
    {
        $result = $this->client->request($method, $url, $data);
        $this->response = new ResponseData($this->client->getStatusCode(), $result);
        $this->logger->info(sprintf('%s %s : %d', $method, $url, $this->client->getStatusCode()), $data);
        $this->repository->save($url, $method, $this->client->getStatusCode());
    }

    public function getResponse(): ResponseData
    {
        return $this->response;
    }
}
