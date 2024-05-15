<?php

namespace App;

class UserInput
{
    private array $arguments;

    public function __construct(array $arguments = [])
    {
        global $argv;
        $this->arguments = array_merge($argv, $arguments);
    }
    public function input(string $prompt): string
    {
        $result = readline($prompt);
        if (empty($result)) {
            throw new \InvalidArgumentException('Empty input');
        }
        return $result;
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
}