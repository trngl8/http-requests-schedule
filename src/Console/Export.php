<?php

namespace App\Console;

use App\Database;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Export extends Command
{
    protected static $defaultName = 'app:export';

    protected function configure()
    {
        $this
            ->setDescription('Export data')
            ->setHelp('This command allows you to export data...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Exporting csv data...');
        $database = new Database('localhost.db');
        $data = $database->fetch('requests', []);
        $fp = fopen(__DIR__ . '/../../var/requests.csv', 'w');
        foreach ($data as $row) {
            $output->writeln(implode(',', $row));
            fputcsv($fp, $row);
        }
        return Command::SUCCESS;
    }
}
