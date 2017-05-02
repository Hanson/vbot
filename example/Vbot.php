<?php

namespace Hanson\Vbot\Example;

use Hanson\Vbot\Example\Modules\MessageModule;
use Hanson\Vbot\Foundation\Vbot as Bot;
use Hanson\Vbot\Message\Text;
use Illuminate\Support\Collection;

class Vbot
{
    private $config;

    public function __construct()
    {
        $this->config = require_once __DIR__.'/config.php';
    }

    public function run()
    {
        $robot = new Bot($this->config);

        $robot->messageHandler->setHandler([MessageModule::class, 'messageHandler']);

//        $robot->messageHandler->setHandler(function(Collection $message){
//            if($message['type'] === 'text' && $message['content'] === 'hi'){
//                Text::send($message['from']['UserName'], 'hi');
////                Text::send('hanson1994', '<msg  username=\'imkuqin\' nickname=\'程序猿\'/>', 42);
//            }
//        });

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
