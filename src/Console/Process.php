<?php

namespace App\Console;

use App\Database;
use App\QueueHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
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
        $this->database = new Database('localhost.db');

        $this->handler = new QueueHandler($this->database);
        $this->handler->setLogger($this->logger);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Process data')
            ->setHelp('This command allows you to process data...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $result = $this->database->fetch('requests', []);
        $headers = ['ID', 'Method', 'URL', 'Headers', 'Body', 'Created at', 'Ran at', 'Finished at'];

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Please select an action',
            ['list', 'import', 'run', 'delete'],
            0
        );
        $question->setErrorMessage('Action %s is invalid.');

        $action = $helper->ask($input, $output, $question);

        switch ($action) {
            case 'list':
                $io->title('Books');
                $io->table(
                    $headers,
                    $result
                );
                break;
            case 'import':
                $result = $io->askQuestion(new Question('Enter filename: '));
                $this->handler->import(__DIR__ . '/../../var/data/' . $result);
                $io->info(sprintf('File %s processed', $result));
                break;
            case 'run':
                $this->handler->init();
                $io->progressStart(count($this->handler->getItems()));

                $i = 0;
                while ($next = $this->handler->getNext()) {
                    try {
                        var_dump($next);
                        $this->handler->process($next);
                        $i++;
                    } catch (\Exception $e) {
                        $output->writeln('<error>'.$e->getMessage().'</error>');
                        $this->logger->error($e->getMessage());
                        sleep(1);
                    }
                    $io->progressAdvance();
                }

                $io->progressFinish();
                $io->info(sprintf('Processed %d requests', $i));
                break;
            case 'delete':
                $io->warning('Deleting data requests... ');
                $this->database->exec('DELETE FROM requests');
                $io->warning('Deleting data responses... ');
                $this->database->exec('DELETE FROM responses');
                $io->info(sprintf('Deleted successfully'));
                break;
            default:
                $io->error('Unknown action');
        }

        return Command::SUCCESS;
    }
}
