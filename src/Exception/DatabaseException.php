<?php

namespace App\Exception;

class DatabaseException extends \Exception implements \Throwable
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
