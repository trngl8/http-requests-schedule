<?php

namespace App;

interface HttpTransportInterface
{
    public function get(string $url): string;
    public function post(string $url, array $data): string;

    public function getStatusCode(): int;
}
