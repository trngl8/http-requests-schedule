<?php

namespace App;

class CurlException extends \Exception implements \Throwable
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
