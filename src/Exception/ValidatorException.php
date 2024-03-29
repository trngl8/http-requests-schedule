<?php

namespace App\Exception;

class ValidatorException extends \Exception implements \Throwable
{
    public function __construct(string $pattern, string $value)
    {
        parent::__construct(sprintf($pattern.": %s", $value));
    }
}
