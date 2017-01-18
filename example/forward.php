<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2016/12/7
 * Time: 16:33
 */

require_once __DIR__ . './../vendor/autoload.php';

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Message;

$robot = new Vbot([
    'tmp' => __DIR__ . '/./../tmp/',
    'debug' => true
]);

$robot->server->setMessageHandler(function($message){
    if($message->type === 'Text'){
        /** @var $message Message */
        $contact = contact()->getUsernameById('hanson1994');
        Text::send($message->content, $contact);
    }
});
$robot->server->setCustomerHandler(function(){
});
$robot->server->run();
