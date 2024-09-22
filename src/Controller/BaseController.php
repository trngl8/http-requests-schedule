<?php

namespace App\Controller;

use App\Database;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class BaseController
{
    protected Database $database;
    private Environment $twig;

    public function __construct(Environment $twig, Database $database)
    {
        $this->twig = $twig;
        $this->database = $database;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    protected function render(string $template, array $data = []): string
    {
        return $this->twig->render($template, $data);
    }
}
