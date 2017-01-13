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
    // 发送撤回消息 （排除自己）
    if($message->type === 'Recall' && $message->rawMsg['FromUserName'] !== myself()->username ){
//        Message::send($message->content, $message->username);
        $message = message()->get($message->msgId);
        $nickname = $message['sender'] ? $message['sender']['NickName'] : account()->getAccount($message['username'])['NickName'];
        $content = "{$nickname} 刚撤回了消息 " . $message['type'] === 'Text' ? "\"{$message['content']}\"" : null;
        if($message['type'] === 'Image'){
            \Hanson\Robot\Message\Image::send($message->username, realpath(__DIR__ . "/./../tmp/jpg/{$message->msgId}.jpg"));
        }else{
            return $content;
        }
    }

});

$robot->server->run();
