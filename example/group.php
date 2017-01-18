<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2016/12/7
 * Time: 16:33
 */

require_once __DIR__ . './../vendor/autoload.php';

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Entity\Text;

$robot = new Vbot([
    'tmp' => __DIR__ . '/./../tmp/',
    'debug' => true
]);

$robot->server->setCustomerHandler(function(){

    $group = group()->getGroupsByNickname('stackoverflow', true)->first();
    Text::send($group['UserName'], '测试' . \Carbon\Carbon::now()->toDateTimeString());

});

$robot->server->run();
