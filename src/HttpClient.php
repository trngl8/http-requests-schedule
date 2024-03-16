<?php

namespace App;

use Monolog\Logger;

class HttpClient
{
    private $response;

    private $logger;

    public function __construct(Logger $logger)
    {
        $this->response = new \stdClass();
        $this->response->statusCode = 404;
        $this->logger = $logger;
    }

    public function get(string $url): void
    {
        try {
            $response = $this->sendRequest('GET', $url);
        } catch (TransportException $e) {
            $this->logger->error($e->getMessage());
            return;
        }

        $this->response = $response;
    }

    public function post(string $url, array $data): void
    {
        try {
            $response = $this->sendRequest('POST', $url, $data);
        } catch (TransportException $e) {
            $this->logger->error($e->getMessage());
            return;
        }

        $this->response = $response;
    }

    public function getResponse(): \stdClass
    {
        return $this->response;
    }

    /**
     * @throws TransportException
     */
    private function sendRequest(string $method, string $url, array $data = []): \stdClass
    {
        $response = new \stdClass();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        } elseif ($method === 'GET') {
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_HTTPGET, true);
        } else {
            throw new TransportException('Invalid method');
        }

        $result = curl_exec($curl);

        if (curl_errno($curl)) {
            $erno = curl_errno($curl);
            $error = curl_error($curl);
            curl_close($curl);
            throw new TransportException(sprintf('Curl transport error: %d %s on url "%s"', $erno, $error, $url));
        }

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $response->statusCode = $status;
        $response->content = $result;

        curl_close($curl);

        return $response;
    }
}
