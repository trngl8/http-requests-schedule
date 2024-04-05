<?php

namespace App;

interface SQLQueryInterface
{
    public function query(string $table, array $where): string;

    public function insert(string $table, array $data): string;

    public function update(string $table, array $data, array $where): string;
}
