<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2016/12/7
 * Time: 16:33.
 */
require_once __DIR__.'./../vendor/autoload.php';

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Entity\Text;
use Hanson\Vbot\Support\Console;

$robot = new Vbot([
    'user_path' => __DIR__.'/./../tmp/',
    'session'   => 'console',
    'debug'     => true,
]);

$robot->server->setMessageHandler(function ($message) {
    if (str_contains($message->content, '设置备注')) {
        $result = contact()->setRemarkName($message->from['UserName'], str_replace('设置备注', '', $message->content));
        Console::log('设置备注：'.($result ? '成功' : '失败'));
    }

    if (str_contains($message->content, '设置置顶')) {
        $result = contact()->setStick($message->from['UserName']);
        Console::log('设置置顶：'.($result ? '成功' : '失败'));
    }

    if (str_contains($message->content, '取消置顶')) {
        $result = contact()->setStick($message->from['UserName'], false);
        Console::log('取消置顶：'.($result ? '成功' : '失败'));
    }

    if (str_contains($message->content, '拉群测试')) {
        $username[] = contact()->getUsernameByAlias('...');
        $username[] = contact()->getUsernameByAlias('...');
        $group = group()->create($username);
        Text::send($group['UserName'], '创建群聊天成功');
    }

    if (str_contains($message->content, '拉人')) {
        $nicknames = explode(',', str_replace('拉人', '', $message->content));
        $members = [];
        foreach ($nicknames as $nickname) {
            $members[] = contact()->getUsernameByNickname($nickname);
        }
        $result = group()->addMember($message->from['UserName'], $members);
        Console::log($result ? '拉人成功' : '拉人失败');
    }

    if (str_contains($message->content, '踢人')) {
        $nicknames = explode(',', str_replace('踢人', '', $message->content));
        $members = [];
        foreach ($nicknames as $nickname) {
            $members[] = contact()->getUsernameByNickname($nickname);
        }
        $result = group()->deleteMember($message->from['UserName'], $members);
        Console::log($result ? '踢人成功' : '踢人失败');
    }

    if (str_contains($message->content, '设置群名称')) {
        $result = group()->setGroupName($message->from['UserName'], str_replace('设置群名称', '', $message->content));
        Console::log('设置群名称：'.($result ? '成功' : '失败'));
    }
});

$robot->server->run();
