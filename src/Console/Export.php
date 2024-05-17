<?php

namespace App\Console;

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
        return Command::SUCCESS;
    }
}
