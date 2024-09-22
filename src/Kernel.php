<?php

namespace App;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\Dotenv\Dotenv;

class Kernel
{
    private string $sourceFolder;

    private Environment $twig;

    private Database $database;

    private bool $debug;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
        $this->sourceFolder =  __DIR__ . '/../';

        $projectDir = __DIR__.'/../';
        $dotenv = new Dotenv();
        $dotenv->load($projectDir . '.env');
        if (file_exists($projectDir . '.env.local')) {
            $dotenv->load($projectDir . '.env.local');
        }

        if (!array_key_exists('APP_DOMAIN', $_ENV)) {
            throw new \Exception('APP_DOMAIN is not defined in .env file');
        }

        if (!array_key_exists('DATABASE_DSN', $_ENV)) {
            $_ENV['DATABASE_DSN'] = sprintf('%s.db', $_ENV['APP_DOMAIN']);
        }

        $this->database = new Database($_ENV['DATABASE_DSN']);

        if ($_ENV['APP_DEBUG'] === 'true') {
            $this->debug = true;
        }

        $twigLoader = new FilesystemLoader($this->sourceFolder . 'templates');
        $this->twig = new Environment($twigLoader, [
            'cache' => $this->sourceFolder . 'var/cache/twig',
            'debug' => $this->debug,
        ]);
    }
    public function getSourceFolder(): string
    {
        return $this->sourceFolder;
    }

    public function getDatabase(): Database
    {
        return $this->database;
    }

    public function getTemplateEngine(): Environment
    {
        return $this->twig;
    }
}
