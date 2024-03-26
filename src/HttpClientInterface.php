<?php

namespace App;

interface HttpClientInterface
{
    public function getStatusCode(): int;

    public function request(string $method, string $url, array $data = []): string;

    public function getHeaders(): array;

    public function getHeader(string $name): string;

    public function getBody(): string;
}
