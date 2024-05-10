<?php

namespace App;

class Command
{
    private $choice = false;
    public function __construct(private string $name)
    {
    }

    public function getArgs(): array
    {
        global $argv;
        $result = [];
        foreach ($argv as $arg) {
            if (str_contains($arg, '=')) {
                [$key, $value] = explode('=', $arg);
                $result[$key] = $value;
            } else {
                $result[$arg] = true;
            }
        }
        return $result;
    }

    public function run(bool $force = false): void
    {
        if ($force || array_key_exists('--force', $this->getArgs())) {
            $choice = 'y';
        } else {
            $choice = readline(sprintf('This script %s will remove your records in the database. Are you agree? [y/n]: ', $this->name));
        }

        if ($choice !== 'y') {
            echo "OK. \n";
            exit;
        }


    }

    public function getResult(): string
    {
        return 'y';
    }
}
