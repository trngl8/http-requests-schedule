<?php

namespace App;

class Command
{
    private bool $choice = false;

    private string $prompt = 'This script %s will remove your records in the database. Are you agree? [y/n]: ';

    private UserInput $userInput;

    public function __construct(private readonly string $name)
    {
    }

    public static function createInput(array $arguments = []): UserInput
    {
        $force = false;
        global $argv;
        $arguments = array_merge($argv, $arguments);
        if (in_array('--force', $arguments)) {
            $force = true;
        }

        return new UserInput($force, $arguments);
    }

    public function setUserInput(UserInput $userInput): void
    {
        $this->userInput = $userInput;
    }

    public function choice(): void
    {
        $value = $this->userInput->getForce() ? 'y' : $this->userInput->input(sprintf($this->prompt, $this->name));

        $this->choice = $value === 'y';
    }

    public function getChoice(): bool
    {
        return $this->choice;
    }

    public function getValue($prompt): string
    {
        return $this->userInput->input($prompt);
    }
}
