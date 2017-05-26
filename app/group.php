<?php

require_once __DIR__ . './../vendor/autoload.php';

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Support\Console;
use Hanson\Vbot\Message\Entity\Text;

//设置时区
date_default_timezone_set('Asia/Shanghai');

$robot = new Vbot([
    'user_path' => __DIR__ . '/./../tmp/',
    'debug' => true
]);

function reply($str)
{
    return http()->post('http://www.tuling123.com/openapi/api', [
        'key' => '1dce02aef026258eff69635a06b0ab7d',
        'info' => $str
    ], true)['text'];

}

$robot->server->setMessageHandler(function ($message) {
    $config = [
        'JQQTSRY1' => '测试群1',
        'JQQTSRY2' => '测试群2',
        'JQJZEXK1' => '测试群3',
        'JQJZEXK2' => '测试群4'
    ];
    $content = trim($message->content);

    if(isset($config[$content])) {
        //拉人进群
        $member = $message->from['UserName'];
        $groupObj = group()->getGroupsByNickname($config[$content]);
        $group = $groupObj->first();
        if(!empty($group)) {
            $result = group()->addMember($group['UserName'], $member);
            Console::log($result ? '拉人成功:' . $message->from['NickName']  : '拉人失败:' . $message->from['NickName']);
        } else {
            Console::log('没有发现群:' . $config[$content]);
        }
    } else {
        // 文字信息
        if ($message instanceof Text) {
            // 联系人自动回复
            if ($message->fromType === 'Contact') {
                return reply($content);
            }
        }
    }
});

$robot->server->setExitHandler(function () {
    \Hanson\Vbot\Support\Console::log('其他设备登录');
});

$robot->server->setExceptionHandler(function () {
    \Hanson\Vbot\Support\Console::log('异常退出');
});

$robot->server->run();