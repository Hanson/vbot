<?php


namespace Hanson\Vbot\Foundation;


use Hanson\Vbot\Console\Command;
use Hanson\Vbot\Exceptions\ConfigErrorException;
use Hanson\Vbot\Foundation\Config;

class Kernel
{
    private $vbot;

    public function __construct($vbot)
    {
        $this->vbot = $vbot;
    }
    public function bootstrap()
    {
        $this->bootstrapLog();
        $this->bootstrapException();
        $this->bootstrapConfig();
        $this->bootstrapPath();
    }

    private function bootstrapLog()
    {
        
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
//        $session = Config::get('session', );
        (new Command)->register();
    }

    private function bootstrapPath()
    {
        if(!$path = Config::get('path')){
            throw new ConfigErrorException('path not set.');
        }

//        echo $path;
//        echo realpath($path);
    }
}