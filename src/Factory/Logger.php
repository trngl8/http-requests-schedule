<?php

namespace App\Factory;

use Monolog\Logger as MonologLogger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

class Logger
{
    public static function create(string $name): MonologLogger
    {
        $logger = new MonologLogger($name);
        $logger->pushHandler(new StreamHandler(__DIR__ . '/../../var/logs/' . $name . '.log', Level::Info));
        return $logger;
    }
}
