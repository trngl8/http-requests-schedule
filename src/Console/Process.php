<?php

namespace App\Console;

use App\Database;
use App\QueueHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Process extends Command
{
    private Logger $logger;

    private Database $database;

    private QueueHandler $handler;

    protected static string $defaultName = 'app:process';

    public function __construct(string $name = null)
    {
        parent::__construct($name);

        $this->logger = new Logger('queue');
        $this->database = new Database('requests');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../var/logs/http-queue.log', Level::Info));

        $this->handler = new QueueHandler($this->logger, $this->database);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Process data')
            ->setHelp('This command allows you to process data...')
            ->addArgument('action', InputArgument::REQUIRED, 'Any process action');

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $result = $this->database->fetch('requests', []);
        $headers = ['ID', 'Method', 'URL', 'Headers', 'Body', 'Created at', 'Ran at', 'Finished at'];

        $action = $input->getArgument('action');

        switch ($action) {
            case 'show':
                $io->title('Books');
                $io->table(
                    $headers,
                    $result
                );
                break;
            case 'run':
                $output->writeln('<info>Processing data... </info>');

                $this->handler->run();

                $output->writeln('<info>Processed </info>');

                break;
            case 'delete':
                $output->writeln('Deleting data... ');
                $this->database->exec('DELETE FROM requests');
                $this->database->exec('DELETE FROM responses');
                break;
            default:
                $output->writeln('Unknown action');
        }

        return Command::SUCCESS;
    }
}
