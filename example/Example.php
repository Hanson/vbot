<?php

namespace Hanson\Vbot\Example;


use Hanson\Vbot\Foundation\Vbot;
require __DIR__.'/../vendor/autoload.php';
class Example
{
    private $config;

    public function __construct()
    {
        $path = __DIR__.'/./../tmp/';
        $this->config = [
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
            'cache' => [
                'default' => 'file',
                'stores'  => [
                    'file' => [
                        'driver' => 'file',
                        'path'   => $path.'cache',
                    ],
                    'redis' => [
                        'driver' => 'redis',
                        'connection' => 'default',
                    ],
                ],
            ],
            'database' => [
                'redis' => [
                    'client' => 'predis',
                    'default' => [
                        'host' => '127.0.0.1',
                        'password' => null,
                        'port' => 6379,
                        'database' => 13,
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
        ];
    }

    public function run()
    {
        $robot = new Vbot($this->config);

        //$robot->server->setMessageHandler(function($message){
        //    ;
        //});

        $robot->exception->setHandler([ExceptionHandler::class, 'handler']);

        $robot->observer->setQrCodeObserver([Observer::class, 'setQrCodeObserver']);

        $robot->observer->setLoginSuccessObserver([Observer::class, 'setLoginSuccessObserver']);

        $robot->observer->setReLoginSuccessObserver([Observer::class, 'setReLoginSuccessObserver']);

        $robot->observer->setExitObserver([Observer::class, 'setExitObserver']);

        //$robot->qrCodeObserver->trigger('abc');

        $robot->server->serve();
   }
}

$vbot = new Example();

$vbot->run();
