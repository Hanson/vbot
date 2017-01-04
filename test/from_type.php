<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2016/12/7
 * Time: 16:33
 */

require_once __DIR__ . './../vendor/autoload.php';

$robot = new \Hanson\Robot\Foundation\Robot([
    'tmp' => realpath('./tmp') . '/',
    'debug' => true,
]);

$client = new \GuzzleHttp\Client();

$robot->server->setMessageHandler(function($message) use ($client, $robot){
    /** @var $message \Hanson\Robot\Message\Message */
    if($message->type === 'Text'){
        \Hanson\Robot\Message\Message::send($message->fromType, $message->username);
    }
});
$robot->server->run();
