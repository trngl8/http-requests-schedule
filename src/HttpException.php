<?php

namespace App;

class HttpException extends \Exception implements \Throwable
{
    public function __construct(string $message, int $code = 500)
    {
        parent::__construct($message, $code);
    }
}
