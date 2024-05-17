<?php

namespace App;

interface TransportInterface
{
    public function init(string $url): void;

    public function addOption(int $name, $value): self;

    public function execute(): string;

    public function getInfo(int $name): mixed;

    public function close(): void;

    public function getError(): string;

    public function getErrno(): int;
}
