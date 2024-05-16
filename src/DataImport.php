<?php

namespace App;

use App\Exception\DatabaseException;
class DataImport
{
    private Database $database;

    private $errors = [];

    public function setDatabase(Database $database): self
    {
        $this->database = $database;
        return $this;
    }

    public function processLines(array $lines): int
    {
        $c = 0;
        foreach ($lines as $k => $line) {
            $res = explode(',', $line);
            try {
                $this->database->insert('requests', $res);
                $c++;
            } catch (DatabaseException $e) {
                $this->errors[$k] = $e->getMessage();
            }
        }

        return $c;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
