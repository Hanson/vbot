<?php

namespace Hanson\Vbot\Example\Handlers\Contact;

use Hanson\Vbot\Contact\Friends;
use Hanson\Vbot\Contact\Groups;
use Hanson\Vbot\Message\Text;
use Illuminate\Support\Collection;

class ColleagueGroup
{
    public static function messageHandler(Collection $message, Friends $friends, Groups $groups)
    {
        if ($message['from']['NickName'] === '三年二班') {
            if ($message['type'] === 'text' && str_contains($message['content'], '餐费')) {
                $str = str_replace('餐费', '', $message['content']);
                $array = explode(' ', $str);
                if (count($array) !== 2) {
                    return;
                }
                $total = current($array);
                $every = explode(',', $array[1]);
                $origin = array_sum($every);
                if ($origin === 0) {
                    return;
                }
                $result = "餐费分别为：\n";
                foreach ($every as $each) {
                    $result .= strval(round($each / $origin * $total, 2)).PHP_EOL;
                }
                Text::send($message['from']['UserName'], $result);
            }
        }
    }
}
