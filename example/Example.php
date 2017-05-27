<?php

namespace Hanson\Vbot\Example;

use Hanson\Vbot\Foundation\Vbot as Bot;

class Example
{
    private $config;

    public function __construct()
    {
        $this->config = require_once __DIR__.'/config.php';
    }

    public function run()
    {
        $robot = new Bot($this->config);

        $robot->messageHandler->setHandler([MessageHandler::class, 'messageHandler']);

        $robot->observer->setQrCodeObserver([Observer::class, 'setQrCodeObserver']);

        $robot->observer->setLoginSuccessObserver([Observer::class, 'setLoginSuccessObserver']);

        $robot->observer->setReLoginSuccessObserver([Observer::class, 'setReLoginSuccessObserver']);

        $robot->observer->setExitObserver([Observer::class, 'setExitObserver']);

        $robot->observer->setFetchContactObserver([Observer::class, 'setFetchContactObserver']);

        $robot->observer->setBeforeMessageObserver([Observer::class, 'setBeforeMessageObserver']);

        $robot->observer->setNeedActivateObserver([Observer::class, 'setNeedActivateObserver']);

        $robot->server->serve();
    }
}
