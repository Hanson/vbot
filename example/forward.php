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

$robot = new Vbot([
    'user_path' => __DIR__.'/./../tmp/',
    'session'   => 'console',
    'debug'     => true,
]);

$robot->server->setMessageHandler(function ($message) {
    if ($message instanceof Text) {
        /** @var $message Text */
        $contact = contact()->getUsernameByAlias('hanson');
        Text::send($contact, $message);
    }
});

$robot->server->run();
