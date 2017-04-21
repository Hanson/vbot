<?php


namespace Hanson\Vbot\Foundation;

use Hanson\Vbot\Console\Command;
use Hanson\Vbot\Exceptions\ConfigErrorException;
use Hanson\Vbot\Foundation\Config;
use Hanson\Vbot\Session\Session;

class Kernel
{
    /**
     * @var Vbot
     */
    private $vbot;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }
    public function bootstrap()
    {
        $this->registerProviders();
        $this->bootstrapLog();
        $this->bootstrapException();
        $this->bootstrapConfig();
        $this->bootstrapPath();
    }

    private function bootstrapLog()
    {
    }

    private function registerProviders()
    {
        $this->vbot->registerProviders();
    }

    private function bootstrapException()
    {
        error_reporting(-1);
        set_error_handler([$this->vbot->exception, 'handleError']);
        set_exception_handler([$this->vbot->exception, 'handleException']);
        register_shutdown_function([$this->vbot->exception, 'handleShutdown']);
    }

    private function bootstrapConfig()
    {
        $session = Session::currentSession();

        echo $session;
    }

    private function bootstrapPath()
    {
        if (!$path = $this->vbot->config['path']) {
            throw new ConfigErrorException('path not set.');
        }

//        echo $path;
//        echo realpath($path);
    }
}
