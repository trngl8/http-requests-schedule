<?php

namespace App;

class Command
{
    private bool $choice = false;

    private string $output = "OK. \n";

    private string $prompt = 'This script %s will remove your records in the database. Are you agree? [y/n]: ';

    private array $arguments;

    public function __construct(private readonly string $name, array $arguments = [])
    {
        global $argv;
        $this->arguments = array_merge($argv, $arguments);
    }

    public function getArgs(): array
    {
        $result = [];
        foreach ($this->arguments as $arg) {
            if (str_contains($arg, '=')) {
                [$key, $value] = explode('=', $arg);
                $result[$key] = $value;
            } else {
                $result[$arg] = true;
            }
        }
        return $result;
    }

    public function choice(bool $force = false): void
    {
        if ($force || array_key_exists('--force', $this->getArgs())) {
            $value = 'y';
        } else {
            // cannot be covered by unit test
            $value = readline(sprintf($this->prompt, $this->name));
        }

        if (!$this->choice && (empty($value) || $value === 'y') ) {
            $this->choice = true;
            return;
        }

        $this->choice = false;

        echo $this->output;
        exit;
    }

    public function getChoice(): bool
    {
        return $this->choice;
    }
}
