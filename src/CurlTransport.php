<?php

namespace App;

class CurlTransport implements HttpTransportInterface
{
    private int $status;

    /**
     * @throws CurlException
     */
    public function get(string $url): string
    {
        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new CurlException(sprintf('Invalid URL %s', $url));
        }

        return $this->sendRequest($url);
    }

    /**
     * @throws CurlException
     */
    public function post(string $url, array $data): string
    {
        if(!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new CurlException(sprintf('Invalid URL %s', $url));
        }

        return $this->sendRequest($url, 'POST', $data);
    }

    /**
     * @throws CurlException
     */
    public function sendRequest(string $url, string $method= 'GET', array $data = []): string
    {
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

        if ($result) {
            $this->status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            return $result;
        }

        $erno = curl_errno($curl);
        $error = curl_error($curl);
        curl_close($curl);
        throw new CurlException(sprintf('Curl transport error: %d %s on url "%s"', $erno, $error, $url));
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }
}
