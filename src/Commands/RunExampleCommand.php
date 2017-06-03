<?php

namespace Hanson\Vbot\Commands;

use Hanson\Vbot\Example\Example;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunExampleCommand extends SymfonyCommand
{
    protected function configure()
    {
        $this->setName('run:example')
            ->addOption('session', null, InputOption::VALUE_REQUIRED)
            ->setDescription('Running a example script that Vbot provided.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $session = $input->getOption('session');

        $vbot = new Example($session);

        $vbot->run();
    }
}
