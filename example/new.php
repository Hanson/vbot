<?php

//namespace Example;

//use Hanson\Vbot\Foundation\Config;
use Hanson\Vbot\Foundation\Vbot;

require_once __DIR__ . './../vendor/autoload.php';

$path = __DIR__ . '/./../tmp/';
$robot = new Vbot([
    'user_path' => $path,
    'debug' => true,
]);


//$robot->server->setMessageHandler(function($message){
//    ;
//});

$robot->exception->setHandler(function(Exception $e){
    echo $e->getMessage();
    return false;
});


$robot->server->serve();