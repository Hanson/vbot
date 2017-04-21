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

$robot = new Vbot([
    'user_path' => __DIR__.'/./../tmp/',
    'session'   => 'console',
    'debug'     => true,
]);

$robot->server->setCustomerHandler(function () {
    $whiteList = ['some remark name...', 'some remark name...'];
    $blackList = ['some remark name...', 'some remark name...'];
    contact()->each(function ($item, $username) use ($whiteList, $blackList) {
        // 发送白名单
        if ($item['RemarkName'] && in_array($item['RemarkName'], $whiteList)) {
            Text::send($username, $item['RemarkName'].' 新年快乐');
            sleep(2);
        }
        // 黑名单不发送
        //        if($item['RemarkName'] && !in_array($item['RemarkName'], $blackList)){
        //            Text::send($username, $item['RemarkName'] . ' 新年快乐');
        //        }
        // 全部人发送
        //        if($item['RemarkName']){
        //            Text::send($username, $item['RemarkName'] . ' 新年快乐');
        //        }
    });
    exit;
});

$robot->server->run();
