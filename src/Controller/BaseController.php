<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

abstract class BaseController
{
    private Request $request;

    private Environment $twig;

    public function __construct(Environment $twig, Request $request)
    {
        $this->request = $request;
        $this->twig = $twig;
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