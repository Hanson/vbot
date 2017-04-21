<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2016/12/7
 * Time: 16:33.
 */
require_once __DIR__.'./../vendor/autoload.php';

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Entity\Text;
use Hanson\Vbot\Support\Console;

$robot = new Vbot([
    'user_path' => __DIR__.'/./../tmp/',
    'session'   => 'console',
    'debug'     => true,
]);

$robot->server->setCustomerHandler(function () {
    contact()->each(function ($item, $username) {
        $word = '新年快乐';
        Console::log("send to username: $username  nickname:{$item['NickName']}");
        Text::send($username, $word);
        sleep(2);
    });
    exit;
});

$robot->server->run();
