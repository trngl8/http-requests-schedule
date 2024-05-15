<?php

namespace App;

class UserInput
{
    private array $arguments;

    private bool $force = false;

    public function __construct(bool $force, array $arguments)
    {
        $this->force = $force;
        $this->arguments = $arguments;
    }

    public function input(string $prompt): string
    {
        if ($this->force) {
            return 'y';
        }
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

    public function getForce(): bool
    {
        return $this->force;
    }
}
