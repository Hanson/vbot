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
    public function register()
    {
        $application = new Application();

        $application->add(new ClearSessionCommand());
//        $input = new ArgvInput();
        $input = new InputDefinition(array(
            // ...
            new InputArgument('session', InputArgument::OPTIONAL),
        ));
        print_r($input->getArguments()[0]->get);
//        $application->run();
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static())->$method(...$parameters);
    }

}