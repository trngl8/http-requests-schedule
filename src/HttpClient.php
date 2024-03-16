<?php

namespace App;

class HttpClient
{
    private $response;

    public function __construct()
    {
        $this->response = new \stdClass();
        $this->response->statusCode = 404;
    }

    public function get(string $url): void
    {
        try {
            $response = $this->sedRequest($url);
        } catch (TransportException $e) {
            echo $e->getMessage();
            return;
        }

        $this->response = $response;
    }

    public function getResponse(): \stdClass
    {
        return $this->response;
    }

    private function sedRequest(string $url): \stdClass
    {
        $response = new \stdClass();

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $result = curl_exec($curl);

        if (curl_errno($curl)) {
            curl_close($curl);
            throw new TransportException(sprintf('Curl error: %d %s', curl_errno($curl), curl_error($curl)));
        }

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $response->statusCode = $status;
        $response->content = $result;

        curl_close($curl);

        return $response;
    }
}
