<?php

namespace Hanson\Vbot\Commands;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationCommand extends SymfonyCommand
{
    protected function configure()
    {
        $this->setName('vbot:clear:session')
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
    }
}
