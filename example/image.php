<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2016/12/7
 * Time: 16:33
 */

require_once __DIR__ . './../vendor/autoload.php';

use Hanson\Vbot\Foundation\Robot;
use Hanson\Vbot\Message\Message;
use Hanson\Vbot\Message\Image;
use Hanson\Vbot\Support\Console;

$robot = new Robot([
    'tmp' => __DIR__ . '/./../tmp/',
]);

$robot->server->setMessageHandler(function($message){
    /** @var $message Message */
    if($message->content === '测试图片'){
        // 自己发就发送给自己
        Image::send($message->username, realpath(__DIR__ . '/./../tmp/jpg/1547651860337387181.jpg'));
    }
    if($message->type === 'Recall' && $message->rawMsg['FromUserName'] !== myself()->username){
        Console::log($message->content);
        Text::send($message->content, $message->username);
        Image::send($message->username, realpath(__DIR__ . '/./../tmp/jpg/1547651860337387181.jpg'));
    }

});

$robot->server->run();
