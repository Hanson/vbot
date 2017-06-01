<?php


namespace Hanson\Vbot\Example\Handlers\Service;


use Hanson\Vbot\Message\Text;
use Illuminate\Support\Collection;

class GuessNumber
{
    private static $array = [];

    public static function messageHandler(Collection $message)
    {
        if ($message['type'] === 'text' && $message['fromType'] === 'Group') {
            $username = $message['from']['UserName'];

            $isBegin = isset(static::$array[$username]);

            if ($message['pure'] === '猜数字') {
                if ($isBegin) {
                    Text::send($username, '猜数字已经开始，还没结束呢');
                    Text::send($username, '当前区间为：' . static::$array[$username]['begin'].' 到 '.static::$array[$username]['end']);
                }else{
                    Text::send($username, '猜数字开始，请猜一个 1 ~ 99 的数字，中了就赢了哦');
                    static::$array[$username] = [
                        'begin' => 0,
                        'end' => 100,
                        'target' => random_int(1, 99)
                    ];
                }
            } elseif (is_numeric($message['content']) && $isBegin) {
                $message['content'] = intval($message['content']);
                $target = static::$array[$username]['target'];
                if ($message['content'] > static::$array[$username]['begin'] && $message['content'] < static::$array[$username]['end']) {
                    if ($message['content'] == $target) {
                        Text::send($username, $message['sender']['NickName'].'你赢了！数字就为：'.static::$array[$username]['target']);
                        unset(static::$array[$username]);
                    } elseif ($message['content'] > $target) {
                            Text::send($username, '当前区间为：' . static::$array[$username]['begin'].' 到 '.$message['content']);
                            static::$array[$username]['end'] = $message['content'];
                    } else {
                        Text::send($username, '当前区间为：' . $message['content'].' 到 '.static::$array[$username]['end']);
                        static::$array[$username]['begin'] = $message['content'];
                    }
                }
            }
        }
    }
}