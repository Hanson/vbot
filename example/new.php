<?php

namespace Example;

use Hanson\Vbot\Foundation\Config;
use Hanson\Vbot\Foundation\Vbot;

require_once __DIR__ . './../vendor/autoload.php';

$path = __DIR__ . '/./../tmp/';
$robot = new Vbot([
    'user_path' => $path,
    'debug' => true,
]);

echo Config::get('user_path');
Config::set('abc', 'def');
echo Config::get('abc');

//$robot->setMessageHandler(function($message){
//    ;
//});
//
//
//$robot->serve();