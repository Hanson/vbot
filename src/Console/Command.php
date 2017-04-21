<?php


namespace Hanson\Vbot\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class Command
 * @package Hanson\Vbot\Console
 */
class Command
{
    public function run()
    {
        $application = new Application();

        $application->add(new ClearSessionCommand());

        $application->run();
    }
}
