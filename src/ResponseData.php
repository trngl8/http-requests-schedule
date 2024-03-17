<?php

namespace App;

class ResponseData
{
    public int $statusCode;
    public string $body;
    public array $headers;

    public function __construct(int $statusCode, string $body, array $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->body = $body;
        $this->headers = $headers;
    }
}