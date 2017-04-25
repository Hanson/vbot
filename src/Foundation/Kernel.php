<?php

namespace Hanson\Vbot\Foundation;

use Hanson\Vbot\Foundation\ServiceProviders\DatabaseServiceProvider;
use Hanson\Vbot\Session\Session;
use Illuminate\Database\Capsule\Manager;

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
        $this->bootstrapException();
        $this->initializeConfig();
        $this->prepareSession();
        $this->initializePath();
        $this->setDatabase();
    }

    private function registerProviders()
    {
        $this->vbot->registerProviders();
    }

    private function prepareSession()
    {
        $session = new Session($this->vbot);

        $sessionKey = $session->currentSession();
        
        $this->vbot->config['session'] = $sessionKey;
        $this->vbot->config['session_key'] = 'session.'.$sessionKey;
    }

    private function bootstrapException()
    {
        error_reporting(-1);
        set_error_handler([$this->vbot->exception, 'handleError']);
        set_exception_handler([$this->vbot->exception, 'handleException']);
        register_shutdown_function([$this->vbot->exception, 'handleShutdown']);
    }

    /**
     * initialize config.
     */
    private function initializeConfig()
    {
        if (!is_dir($this->vbot->config['path'])) {
            mkdir($this->vbot->config['path'], 0755, true);
        }

        $this->vbot->config['storage'] = $this->vbot->config['storage'] ?: 'collection';

        $this->vbot->config['path'] = realpath($this->vbot->config['path']);
    }

    private function initializePath()
    {
        if (!is_dir($this->vbot->config['path'].'/cookies')) {
            mkdir($this->vbot->config['path'].'/cookies', 0755, true);
        }

        $this->vbot->config['cookie_file'] = $this->vbot->config['path'].'/cookies/'.$this->vbot->config['session'];
    }

    private function setDatabase()
    {
        if($this->vbot->config['storage'] === 'database'){
            $capsule = new Manager;

            $capsule->addConnection($this->vbot->config['database.mysql']);

            $capsule->setAsGlobal();

            (new DatabaseServiceProvider())->register($this->vbot);
        }
    }
}
