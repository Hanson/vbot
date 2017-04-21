<?php

//namespace Example;

//use Hanson\Vbot\Foundation\Config;
use Hanson\Vbot\Foundation\Vbot;

require_once __DIR__ . './../vendor/autoload.php';

$path = __DIR__ . '/./../tmp/';
$robot = new Vbot([
    'path'     => $path,
    'debug'    => true,
    'download' => [
        'image'   => true,
        'voice'   => true,
        'video'   => true,
        'emotion' => true,
    ],
    'log'      => [
        'level'      => 'debug',
        'permission' => 0777,
        'file'       => $path . '/vbot.log',
    ],
    'cache.default' => 'file',
    'cache.stores.file' => [
        'driver' => 'file',
        'path' => __DIR__.'/../tmp'
    ]
]);


//$robot->server->setMessageHandler(function($message){
//    ;
//});

$robot->exception->setHandler(function (Exception $e) {
    echo $e->getMessage();
    return false;
});


$robot->server->serve();
