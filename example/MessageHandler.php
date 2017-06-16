<?php

namespace Hanson\Vbot\Example;

use Hanson\Vbot\Contact\Friends;
use Hanson\Vbot\Contact\Groups;
use Hanson\Vbot\Contact\Members;
use Hanson\Vbot\Example\Handlers\Contact\ColleagueGroup;
use Hanson\Vbot\Example\Handlers\Contact\ExperienceGroup;
use Hanson\Vbot\Example\Handlers\Contact\FeedbackGroup;
use Hanson\Vbot\Example\Handlers\Contact\Hanson;
use Hanson\Vbot\Example\Handlers\Type\RecallType;
use Hanson\Vbot\Example\Handlers\Type\TextType;
use Hanson\Vbot\Message\Emoticon;
use Hanson\Vbot\Message\Text;
use Illuminate\Support\Collection;

class MessageHandler
{
    public static function messageHandler(Collection $message)
    {
        /** @var Friends $friends */
        $friends = vbot('friends');

        /** @var Members $members */
        $members = vbot('members');

        /** @var Groups $groups */
        $groups = vbot('groups');

        if ($message['type'] === 'touch') {
            //            Text::send($message['raw']['ToUserName'], $message['content']);
        }

        Hanson::messageHandler($message, $friends, $groups);
        ColleagueGroup::messageHandler($message, $friends, $groups);
        FeedbackGroup::messageHandler($message, $friends, $groups);
        ExperienceGroup::messageHandler($message, $friends, $groups);

        TextType::messageHandler($message, $friends, $groups);
        RecallType::messageHandler($message);

        if ($message['type'] === 'new_friend') {
            Text::send($message['from']['UserName'], '客官，等你很久了！感谢跟 vbot 交朋友，如果可以帮我点个star，谢谢了！https://github.com/HanSon/vbot');
            $groups->addMember($groups->getUsernameByNickname('Vbot 体验群'), $message['from']['UserName']);
            Text::send($message['from']['UserName'], '现在拉你进去vbot的测试群，进去后为了避免轰炸记得设置免骚扰哦！如果被不小心踢出群，跟我说声“拉我”我就会拉你进群的了。');
        }

        if ($message['type'] === 'emoticon' && random_int(0, 1)) {
            Emoticon::sendRandom($message['from']['UserName']);
        }

        // @todo
        if ($message['type'] === 'official') {
            vbot('console')->log('收到公众号消息:'.$message['title'].$message['description'].
                $message['app'].$message['url']);
        }

        if ($message['type'] === 'request_friend') {
            vbot('console')->log('收到好友申请:'.$message['info']['Content'].$message['avatar']);
            if ($message['info']['Content'] === 'echo') {
                $friends->approve($message);
            }
        }
    }
}
