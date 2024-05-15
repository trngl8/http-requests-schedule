<?php

namespace App;

class Command
{
    private bool $choice = false;

    private string $output = "OK. \n";

    private string $prompt = 'This script %s will remove your records in the database. Are you agree? [y/n]: ';

    private UserInput $userInput;

    public function __construct(private readonly string $name)
    {
    }

    public static function createInput(array $arguments = []): UserInput
    {
        return new UserInput($arguments);
    }

    public function setUserInput(UserInput $userInput): void
    {
        $this->userInput = $userInput;
    }

    public function choice(bool $force = false): void
    {
        if ($force || array_key_exists('--force', $this->userInput->getArgs())) {
            $value = 'y';
        } else {
            $value = $this->userInput->input(sprintf($this->prompt, $this->name));
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
