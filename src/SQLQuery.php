<?php

namespace App;

class SQLQuery implements SQLQueryInterface
{
    public function insert(string $table, array $data): string
    {
        $fields = implode(', ', array_keys($data));
        $values = implode(', ', array_map(fn($v) => "'$v'", $data));
        return "INSERT INTO $table ($fields) VALUES ($values)";
    }

    public function update(string $table, array $data, array $where): string
    {
        $set = implode(', ', array_map(fn($k, $v) => "$k = '$v'", array_keys($data), $data));
        $where = implode(' AND ', array_map(fn($k, $v) => "$k = '$v'", array_keys($where), $where));
        return "UPDATE $table SET $set WHERE $where";
    }

    public function delete(string $table, array $where): string
    {
        $where = implode(' AND ', array_map(fn($k, $v) => "$k = '$v'", array_keys($where), $where));
        return "DELETE FROM $table WHERE $where";
    }

    public function query(string $table, array $where): string
    {
        $condition = [];
        foreach ($where as $key => $value) {
            if (is_array($value)) {
                $condition[] = "$key IN ('" . implode("', '", $value) . "')";
            }
            if (is_null($value)) {
                $condition[] = "$key IS NULL";
            }
        }
        $conditions = implode(' AND ', $condition);
        return "SELECT * FROM $table WHERE $conditions";
    }
}
