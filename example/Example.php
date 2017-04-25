<?php

namespace Hanson\Vbot\Example;

use Hanson\Vbot\Foundation\Vbot;

require __DIR__.'/../vendor/autoload.php';
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

        $robot->exception->setHandler([\Hanson\Vbot\Example\ExceptionHandler::class, 'handler']);

        $robot->observer->setQrCodeObserver([\Hanson\Vbot\Example\Observer::class, 'setQrCodeObserver']);

        $robot->observer->setLoginSuccessObserver([\Hanson\Vbot\Example\Observer::class, 'setLoginSuccessObserver']);

        $robot->observer->setReLoginSuccessObserver([\Hanson\Vbot\Example\Observer::class, 'setReLoginSuccessObserver']);

        $robot->observer->setExitObserver([\Hanson\Vbot\Example\Observer::class, 'setExitObserver']);

        //$robot->qrCodeObserver->trigger('abc');

        $robot->server->serve();
    }
}

$vbot = new Example();

$vbot->run();
