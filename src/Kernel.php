<?php

namespace App;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\Dotenv\Dotenv;

class Kernel
{
    private string $sourceFolder;

    private Environment $twig;

    private bool $debug;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
        $this->sourceFolder =  __DIR__ . '/../';

        $projectDir = __DIR__.'/../';
        $dotenv = new Dotenv();
        $dotenv->load($projectDir . '.env');

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

    public function getTemplateEngine(): Environment
    {
        return $this->twig;
    }
}
