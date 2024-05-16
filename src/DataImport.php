<?php

namespace App;

use App\Exception\DatabaseException;
class DataImport
{
    private Database $database;

    public function setDatabase(Database $database): self
    {
        $this->database = $database;
        return $this;
    }

    public function processLines(array $lines): int
    {
        $c = 0;
        foreach ($lines as $line) {
            $res = explode(',', $line);
            try {
                $this->database->insert('requests', $res);
                $c++;
            } catch (DatabaseException $e) {
                echo $e->getMessage() . PHP_EOL;
            }
        }

        return $c;
    }
}
