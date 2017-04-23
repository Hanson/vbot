<?php

//namespace Example;

//use Hanson\Vbot\Foundation\Config;
use Hanson\Vbot\Foundation\Vbot;

require_once __DIR__.'./../vendor/autoload.php';

$path = __DIR__.'/./../tmp/';
$robot = new Vbot([
    'path'     => $path,
    'debug'    => true,
    /*
     * 下载配置项
     */
    'download' => [
        'image'   => true,
        'voice'   => true,
        'video'   => true,
        'emotion' => true,
    ],
    /*
     * 输出配置项
     */
    'console' => [
        'output'  => true, // 是否输出
        'message' => true, // 是否输出接收消息 （若上面为 false 此处无效）
    ],
    /*
     * 日志配置项
     */
    'log'      => [
        'level'      => 'debug',
        'permission' => 0777,
        'file'       => $path.'log/vbot.log',
    ],
    /*
     * 缓存配置项
     */
//    'cache.default'     => 'file',
//    'cache.stores.file' => [
//        'driver' => 'file',
//        'path'   => $path.'cache',
//    ],
    'cache' => [
        'default' => 'file',
        'stores'  => [
            'file' => [
                'driver' => 'file',
                'path'   => $path.'cache',
            ],
            'array' => [
                'driver' => 'array',
            ],
            'redis' => [
                'driver'     => 'redis',
                'connection' => 'default',
            ],
        ],
    ],
    /*
     * 无需配置，系统生成
     */
    'server' => [
          'uuid' => 'IeUYcTY8ZQ==',
          'uri'  => [
              'redirect' => '',
              'file'     => '',
              'push'     => '',
              'base'     => '',
          ],
    ],
]);

//$robot->server->setMessageHandler(function($message){
//    ;
//});

$robot->exception->setHandler(function (Exception $e) {
    if ($e instanceof \Hanson\Vbot\Exceptions\FetchUuidException) {
        echo $e->getMessage();
    } elseif ($e instanceof \Hanson\Vbot\Exceptions\LoginTimeoutException) {
        echo $e->getMessage();
    }

    return true;
});

$robot->observer->setQrCodeObserver(function ($qrCodeUrl) {
    echo $qrCodeUrl;
});

$robot->observer->setLoginSuccessObserver(function () {
    echo '登录成功'.PHP_EOL;
});

//$robot->qrCodeObserver->trigger('abc');

$robot->server->serve();
