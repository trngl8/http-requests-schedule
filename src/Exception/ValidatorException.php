<?php

namespace App\Exception;

class ValidatorException extends \Exception implements \Throwable
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
