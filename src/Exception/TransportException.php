<?php

namespace App\Exception;

class TransportException extends \Exception implements \Throwable
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
