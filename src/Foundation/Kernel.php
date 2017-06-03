<?php

namespace Hanson\Vbot\Foundation;

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
        $this->checkEnvironment();
        $this->registerProviders();
        $this->bootstrapException();
        $this->initializeConfig();
        $this->prepareSession();
        $this->initializePath();
    }

    private function checkEnvironment()
    {
        if (PHP_SAPI !== 'cli') {
            die('Please execute script in terminal!');
        }

        if (version_compare(PHP_VERSION, '7.0.0', '<')) {
            die('Vbot have to run under php 7! Current version is :'.PHP_VERSION);
        }

        $mustExtensions = ['gd', 'fileinfo', 'SimpleXML'];

        $diff = array_diff($mustExtensions, get_loaded_extensions());

        if ($diff) {
            die('Running script failed! please install extensions: '.PHP_EOL.implode("\n", $diff).PHP_EOL);
        }

        if ($this->vbot->config->get('swoole.status') && !in_array('swoole', get_loaded_extensions())) {
            die('Please install extension: swoole. Or you can turn it off in config.'.PHP_EOL);
        }
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

        if (!is_dir($this->vbot->config['path'].'/users')) {
            mkdir($this->vbot->config['path'].'/users', 0755, true);
        }

        if (!is_dir($this->vbot->config['download.emoticon_path'])) {
            mkdir($this->vbot->config['download.emoticon_path'], 0755, true);
        }

        $this->vbot->config['cookie_file'] = $this->vbot->config['path'].'/cookies/'.$this->vbot->config['session'];
        $this->vbot->config['user_path'] = $this->vbot->config['path'].'/users/';
    }
}
