<?php

namespace Hanson\Vbot\Example\Handlers\Contact;

use Hanson\Vbot\Contact\Friends;
use Hanson\Vbot\Contact\Groups;
use Hanson\Vbot\Message\Card;
use Hanson\Vbot\Message\Text;
use Illuminate\Support\Collection;

class FeedbackGroup
{
    public static function messageHandler(Collection $message, Friends $friends, Groups $groups)
    {
        if ($message['from']['NickName'] === 'vbot 反馈群') {
            $experience = '体验流程：
            去到 vbot 的 github 网站，clone 下来，然后 git checkout v2.0 即可。运行命令为 php example.php --session=vbot';
            $rule = '反馈群群规：
                首先感谢大家对vbot的支持，大家来这里是测试体验2.0版本的代码，所以需要大家拥有基础的编程技能。这里可以询问新代码的事情，但不要问低级的问题。因为还在测试中，代码随时会更新，更新后我会提醒大家有新代码，以便更新。谢谢配合。';
            if ($message['type'] === 'text' && $message['content'] === '体验') {
                Text::send($message['from']['UserName'], $experience);
            }

            if ($message['content'] === '群规') {
                Text::send($message['from']['UserName'], $rule);
            }

            if ($message['type'] === 'group_change') {
                if ($message['action'] === 'ADD') {
                    Text::send($message['from']['UserName'], $experience);
                    Text::send($message['from']['UserName'], $rule);
                }
            }

            if ($message['content'] === '名片') {
                Card::send($message['from']['UserName'], 'hanson1994', 'HanSon大人');
            }

            if ($message['content'] === '公众号') {
                Card::send($message['from']['UserName'], 'hgjxxg', '华广计协小哥');
            }
        }
    }
}
