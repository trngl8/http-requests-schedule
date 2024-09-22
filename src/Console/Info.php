<?php

namespace App\Console;

use App\Database;
use App\QueueHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process as ProcessRunner;

class Info extends Command
{
    private Logger $logger;

    private Database $database;

    private QueueHandler $handler;

    protected static string $defaultName = 'app:info';

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->logger = new Logger('queue');
        $this->database = new Database('localhost.db');

        $this->handler = new QueueHandler($this->database);
        $this->handler->setLogger($this->logger);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Show interpreter data')
            ->setHelp('The interpreter version and debug')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $process = new ProcessRunner(['php', '-v']);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $io->info($process->getOutput());

        return Command::SUCCESS;
    }
}
