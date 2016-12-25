<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2016/12/7
 * Time: 16:33
 */

require_once __DIR__ . './../vendor/autoload.php';

$robot = new \Hanson\Robot\Foundation\Robot([
    'tmp' => realpath('./tmp') . '/',
    'debug' => true,
]);

$client = new \GuzzleHttp\Client();

$robot->server->setMessageHandler(function($message) use ($client, $robot){
    $url = 'http://www.tuling123.com/openapi/api';

    $result = $robot->server->http->post($url, [
        'key' => '1dce02aef026258eff69635a06b0ab7d',
        'info' => $message->rawMsg['Content']
    ], true);
    print_r($message);
    print_r($result);
    return $result['text'];

});
$robot->server->run();
