<?php

namespace App;

class HttpClient
{
    private $response;

    public function get(string $url): void
    {
        // TODO check $url
        $this->response = new \stdClass();
        $this->response->statusCode = 200;
    }

    public function getResponse(): \stdClass
    {
        return $this->response;
    }
}
