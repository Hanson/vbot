<?php

require 'database.php';

$path = __DIR__.'/./../tmp/';

return [
    'path'     => $path,
    'debug'    => true,
    'storage'  => 'database', // 默认为 collection
    /*
     * 下载配置项
     */
    'download' => [
        'image'         => true,
        'voice'         => true,
        'video'         => true,
        'emoticon'      => true,
        'file'          => true,
        'emoticon_path' => $path.'emoticons',
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
        'level'         => 'debug',
        'permission'    => 0777,
        'system'        => $path.'log/vbot.log',
        'message'       => $path.'log/message.log',
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
                'driver'     => 'redis',
                'connection' => 'default',
            ],
        ],
    ],
    'database' => [
        'mysql' => [
            'driver'    => 'mysql',
            'host'      => DB_HOST,
            'port'      => DB_PORT,
            'database'  => DB_NAME,
            'username'  => DB_USER,
            'password'  => DB_PASSWORD,
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
        'redis' => [
            'client'  => 'predis',
            'default' => [
                'host'     => '127.0.0.1',
                'password' => null,
                'port'     => 6379,
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
