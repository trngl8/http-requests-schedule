<?php

namespace App;

class CurlTransport implements TransportInterface
{
    private $handler;

    public function init(string $url): void
    {
        $this->handler = curl_init($url);
    }

    public function addOption(int $name, $value): self
    {
        curl_setopt($this->handler, $name, $value);

        return $this;
    }

    public function execute(): string
    {
        return curl_exec($this->handler);
    }

    public function getInfo(int $name): mixed
    {
        return curl_getinfo($this->handler, $name);
    }

    public function close(): void
    {
        curl_close($this->handler);
    }

    public function getError(): string
    {
        return curl_error($this->handler);
    }

    public function getErrno(): int
    {
        return curl_errno($this->handler);
    }

    public function __destruct()
    {
        $this->close();
    }
}
