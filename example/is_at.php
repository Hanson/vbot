<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2016/12/7
 * Time: 16:33
 */

require_once __DIR__ . './../vendor/autoload.php';

use Hanson\Robot\Foundation\Robot;
use Hanson\Robot\Message\Message;
use Hanson\Robot\Support\Console;

$robot = new Robot([
    'tmp' => __DIR__ . '/./../tmp/',
]);

$robot->server->setMessageHandler(function($message){
    /** @var $message Message */
    if($message->fromType === 'Group' && $message->type === 'Text'){
        if($message->isAt){
            $groupName = group()->get($message->username)['NickName'];
            $memberName = member()->get($message->sender['UserName'])['NickName'];
            // ex: wechat群 的 HanSon at 了我
            Console::log("{$groupName}的 {$memberName} at 了我");
        }
    }

});

$robot->server->run();
