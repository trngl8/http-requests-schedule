<?php

namespace App;

use Monolog\Logger;

class HttpClient
{
    private ResponseData $response;

    private HttpTransportInterface $transport;

    private DataRepository $repository;

    private $logger;

    public function __construct(HttpTransportInterface $transport, Logger $logger, DataRepository $repository)
    {
        $this->response = new ResponseData(404, '404 Not Found');
        $this->logger = $logger;
        $this->transport = $transport;
        $this->repository = $repository;
    }

    public function get(string $url): void
    {
        $result = $this->transport->get($url);
        $this->response = new ResponseData($this->transport->getStatusCode(), $result);
        $this->logger->info(sprintf('GET %s : %d', $url, $this->transport->getStatusCode()));
        $this->repository->save($url, 'GET', $this->transport->getStatusCode());
    }

    public function post(string $url, array $data): void
    {
        $result = $this->transport->post($url, $data);
        $this->response = new ResponseData($this->transport->getStatusCode(), $result);
        $this->logger->info(sprintf('POST %s : %d', $url, $this->transport->getStatusCode()), $data);
        $this->repository->save($url, 'POST', $this->transport->getStatusCode());
    }

    public function getResponse(): ResponseData
    {
        return $this->response;
    }
}
