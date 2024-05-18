<?php

namespace App;

use Symfony\Component\HttpFoundation\InputBag;

class InputValidator
{
    public function validate(InputBag $bag): array
    {
        $errors = [];
        if (!$bag->get('url')) {
            $errors['url'] = 'URL is required';
        }
        if (!$bag->get('method')) {
            $errors['method'] = 'Method is required';
        }
        if (!in_array($bag->get('method'), ['GET', 'POST'])) {
            $errors['method'] = 'Invalid method';
        }
        if (!filter_var($bag->get('url'), FILTER_VALIDATE_URL)) {
            $errors['url'] = 'Invalid URL';
        }
        return $errors;
    }
}