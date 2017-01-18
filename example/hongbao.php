<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2016/12/7
 * Time: 16:33
 */

require_once __DIR__ . './../vendor/autoload.php';

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Entity\Message;
use Hanson\Vbot\Message\Entity\RedPacket;
use Hanson\Vbot\Support\Console;

$robot = new Vbot([
    'tmp' => __DIR__ . '/./../tmp/',
]);

$robot->server->setMessageHandler(function($message){
    /** @var $message Message */
    if($message instanceof RedPacket){
        $nickname = account()->getAccount($message->from['UserName'])['NickName'];
        Console::log("收到来自 {$nickname} 的红包");
    }

});

$robot->server->run();
