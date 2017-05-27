<?php

namespace Hanson\Vbot\Commands;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ClearSessionCommand extends SymfonyCommand
{
    protected function configure()
    {
        $this->setName('vbot:clear')
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        echo 'clear session';
    }
}
