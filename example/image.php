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
use Hanson\Robot\Message\Image;
use Hanson\Robot\Support\Console;

$robot = new Robot([
    'tmp' => __DIR__ . '/./../tmp/',
]);

$robot->server->setMessageHandler(function($message){
    /** @var $message Message */
    if($message->content === '测试图片'){
        Image::send($message->username, realpath(__DIR__ . '/./../tmp/jpg/5102437722783331610.jpg'));
    }

});

$robot->server->run();
