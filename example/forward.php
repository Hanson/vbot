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

$robot = new Robot([
    'tmp' => __DIR__ . '/./../tmp/',
    'debug' => true
]);

$robot->server->setMessageHandler(function($message){
    if($message->type === 'Text'){
        /** @var $message Message */
        $contact = contact()->getUsernameById('hanson1994');
        Message::send($message->content, $contact);
    }
});
$robot->server->setCustomerHandler(function(){
});
$robot->server->run();
