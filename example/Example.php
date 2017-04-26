<?php

namespace Hanson\Vbot\Example;

use Hanson\Vbot\Foundation\Vbot;

//require __DIR__.'/../vendor/autoload.php';
class Example
{
    private $config;

    public function __construct()
    {
        $this->config = require_once __DIR__.'/config.php';
    }

    public function run()
    {
        $robot = new Vbot($this->config);

        //$robot->server->setMessageHandler(function($message){
        //    ;
        //});

        $robot->exception->setHandler([ExceptionHandler::class, 'handler']);

        $robot->observer->setQrCodeObserver([Observer::class, 'setQrCodeObserver']);

        $robot->observer->setLoginSuccessObserver([Observer::class, 'setLoginSuccessObserver']);

        $robot->observer->setReLoginSuccessObserver([Observer::class, 'setReLoginSuccessObserver']);

        $robot->observer->setExitObserver([Observer::class, 'setExitObserver']);

        $robot->observer->setFetchContactObserver([Observer::class, 'setFetchContactObserver']);

        $robot->observer->setBeforeMessageObserver([Observer::class, 'setBeforeMessageObserver']);

        //$robot->qrCodeObserver->trigger('abc');

        $robot->server->serve();
    }
}

$vbot = new Example();

$vbot->run();
