<?php

namespace Hanson\Vbot\Console;

use Symfony\Component\Console\Application;

/**
 * Class Command.
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
