<?php

namespace App\Exception;

class ValidatorException extends \Exception implements \Throwable
{
    public function __construct(string $attr, string $value)
    {
        //parent::__construct(sprintf('Validation failed for attribute "%s" with value "%s". %s', $attr, $value, $message));
        parent::__construct(sprintf('Invalid "%s" with value "%s".', $attr, $value));
    }
}
