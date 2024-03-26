<?php

namespace App;

class HttpClient implements HttpClientInterface
{
    private int $status;

    private array $availableMethods = ['GET', 'POST'];

    /**
     * @throws CurlException
     */
    public function request(string $method = 'GET', string $url = '', array $data = []): string
    {
        if (!in_array($method, $this->availableMethods)) {
            throw new CurlException(sprintf('Invalid method %s', $method));
        }

        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new CurlException(sprintf('Invalid URL %s', $url));
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($method === 'GET') {
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_HTTPGET, true);
        }

        if ($method === 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $result = curl_exec($curl);

        if (!$result) {
            $erno = curl_errno($curl);
            $error = curl_error($curl);
            curl_close($curl);
            throw new CurlException(sprintf('Curl transport error: %d %s on url "%s"', $erno, $error, $url));
        }

        $this->status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);
        return $result;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return [];
    }

    public function getHeader(string $name): string
    {
        if (array_key_exists($name, $this->getHeaders())) {
            return $this->getHeaders()[$name];
        }

        return '';
    }

    public function getBody(): string
    {
        return '';
    }
}
