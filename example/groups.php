<?php
/**
 * 发送给所有群消息！！！慎重！！！
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

// 发送给所有群消息！！！慎重！！！
$robot->server->setCustomerHandler(function(){
    /** @var $message Message */

    $groups = group()->getGroupsByNickname('stackoverflow', true);
    foreach ($groups as $group) {
        Message::send('测试' . \Carbon\Carbon::now()->toDateTimeString(), $group['UserName']);
    }

});

$robot->server->run();
