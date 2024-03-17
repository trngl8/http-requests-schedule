<?php

namespace App;

use Monolog\Logger;

class HttpClient
{
    private ResponseData $response;

    private HttpTransportInterface $transport;

    private $logger;

    public function __construct(HttpTransportInterface $transport, Logger $logger)
    {
        $this->response = new ResponseData(404, '404 Not Found');
        $this->logger = $logger;
        $this->transport = $transport;
    }

    public function get(string $url): void
    {
        $result = $this->transport->get($url);
        $this->response = new ResponseData($this->transport->getStatusCode(), $result);
        $this->logger->info(sprintf('GET %s : %d', $url, $this->transport->getStatusCode()));
    }

    public function post(string $url, array $data): void
    {
        $result = $this->transport->post($url, $data);
        $this->response = new ResponseData($this->transport->getStatusCode(), $result);
        $this->logger->info(sprintf('POST %s : %d', $url, $this->transport->getStatusCode()), $data);
    }

    public function getResponse(): ResponseData
    {
        return $this->response;
    }
}
