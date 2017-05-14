<?php

namespace Hanson\Vbot\Example\Handlers\Contact;

use Hanson\Vbot\Contact\Friends;
use Hanson\Vbot\Contact\Groups;
use Hanson\Vbot\Message\Text;
use Illuminate\Support\Collection;

class ExperienceGroup
{
    public static function messageHandler(Collection $message, Friends $friends, Groups $groups)
    {
        if ($message['from']['NickName'] === 'Vbot 体验群') {
            if ($message['type'] === 'group_change') {
                Text::send($message['from']['UserName'], '欢迎新人 '.$message['nickname']);
            }

            if ($message['type'] === 'text') {
                if ($message['sender']['NickName'] === 'HanSon') {
                    if (str_contains($message['content'], '加人')) {
                        $username = str_replace('加人', '', $message['content']);
                        $friends->add($username, '我是你儿子');
                    }
                }

                if (str_contains($message['content'], '搜人')) {
                    $nickname = str_replace('搜人', '', $message['content']);
                    $members = $groups->getMembersByNickname($message['from']['UserName'], $nickname, true);
                    $result = '搜索结果 数量：'.count($members)."\n";
                    foreach ($members as $member) {
                        $result .= $member['NickName'].' '.$member['UserName']."\n";
                    }
                    Text::send($message['from']['UserName'], $result);
                }
            }
        }
    }
}
