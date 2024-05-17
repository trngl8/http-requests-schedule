<?php

namespace App;

use App\Exception\DatabaseException;

class DataImport
{
    public const DEFAULT_SEPARATOR = ',';
    private Database $database;

    private array $errors = [];

    private array $columns = [];

    public function setDatabase(Database $database): self
    {
        $this->database = $database;
        return $this;
    }

    public function initHeaders(string $table, array $headers): void
    {
        $this->columns[$table] = $headers;
    }

    public function processLines(string $table, array $lines): int
    {
        $success = [];
        foreach ($lines as $k => $line) {
            $res = explode(self::DEFAULT_SEPARATOR, $line);
            foreach ($this->columns[$table] as $i => $column) {
                $res[$column] = $res[$i];
                unset($res[$i]);
            }
            try {
                $this->database->insert($table, $res);
                $success[] = $k;
            } catch (DatabaseException $e) {
                $this->errors[$k] = $e->getMessage();
            }
        }

        return count($success);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
