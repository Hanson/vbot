<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2016/12/7
 * Time: 16:33.
 */
require_once __DIR__.'./../vendor/autoload.php';

use Carbon\Carbon;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Entity\Image;
use Hanson\Vbot\Message\Entity\Text;

$robot = new Vbot([
    'user_path' => __DIR__.'/./../tmp/',
    'debug'     => true,
]);

$isSendToday = false;
$isNewDay = false;
$menu = [];

function outputMenu($menu, $canteen)
{
    $order = $canteen === '都城' ? "当前都城菜单：\n" : "当前龙兴菜单：\n";

    foreach ($menu as $item) {
        if ($item['canteen'] === $canteen) {
            $order .= "{$item['nickname']} {$item['food']} {$item['price']}\n";
        }
    }

    return $order;
}

function displayName($array)
{
    if (isset($array['DisplayName']) && $array['DisplayName']) {
        return $array['DisplayName'];
    } else {
        return $array['NickName'];
    }
}

$robot->server->setCustomerHandler(function () use (&$isSendToday, &$isNewDay, &$menu) {
    if (!$isSendToday && Carbon::now()->gt(Carbon::now()->hour(17)->minute(53))) {
        $username = group()->getUsernameByNickname('三年二班');
        Text::send($username, "小伙伴们，报名时间截止到10:30\n
        点餐格式如下：\n
        '点餐都城辣子鸡饭16'，'点餐龙兴冬菇焖鸡饭13'\n
        请记得添加我为好友给我转账当天餐费，加好友验证输入'dmc'即可自动添加好友\n
        想取消点餐输入'取消点餐'即可");
        Image::send($username, __DIR__.'/extra/longxing.jpg');
        Image::send($username, __DIR__.'/extra/ducheng.png');
        $isSendToday = true;
    }

    if (!Carbon::now()->hour) {
        $isNewDay = true;
    }

    if ($isNewDay) {
        $isSendToday = false;
        $menu = [];
    }
});

$robot->server->setMessageHandler(function ($message) use (&$menu) {
    if ($message instanceof Text) {
        /** @var $message Text */
        if ($message->from['NickName'] === '三年二班') {
            if (starts_with($message->content, '点餐')) {
                $content = str_replace('点餐', '', $message->content);
                $canteen = substr($content, 0, 6);
                if (in_array($canteen, ['龙兴', '都城'])) {
                    $foodAndPrice = str_replace($canteen, '', $content);
                    $isMatch = preg_match('/(.+)(\d+)/s', $foodAndPrice, $match);
                    if (!$isMatch) {
                        return '点餐格式不对 或者 请在最后输入价格!';
                    }

                    $menu[$message->sender['UserName']]['nickname'] = displayName($message->sender);
                    $menu[$message->sender['UserName']]['canteen'] = $canteen;
                    $menu[$message->sender['UserName']]['food'] = $match[1];
                    $menu[$message->sender['UserName']]['price'] = $match[2];

                    Text::send($message->from['UserName'], '点餐成功！');

                    return outputMenu($menu, $canteen);
                } else {
                    return '不存在此菜单';
                }
            } elseif ($message->content === '取消点餐') {
                if (isset($menu[$message->sender['UserName']])) {
                    $canteen = $menu[$message->sender['UserName']]['canteen'];
                    unset($menu[$message->sender['UserName']]);
                    Text::send($message->from['UserName'], '取消点餐成功！');

                    return outputMenu($menu, $canteen);
                } else {
                    return '你没有点餐呢！';
                }
            } else {
                \Hanson\Vbot\Support\Console::debug($message->content);
            }
        } else {
            \Hanson\Vbot\Support\Console::debug($message->from['NickName']);
        }
    }
});

$robot->server->run();
