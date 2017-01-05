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

$robot = new Robot([
    'tmp' => __DIR__ . './../tmp/',
]);

$robot->server->setMessageHandler(function($message){
    /** @var $message Message */
    if($message->type === 'Text'){
        $url = 'http://www.tuling123.com/openapi/api';

        $result = http()->post($url, [
            'key' => '1dce02aef026258eff69635a06b0ab7d',
            'info' => $message->content
        ], true);

        return $result['text'];
    }
});
$robot->server->run();
