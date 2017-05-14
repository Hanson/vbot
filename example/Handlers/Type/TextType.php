<?php

namespace Hanson\Vbot\Example\Handlers\Type;

use Hanson\Vbot\Contact\Friends;
use Hanson\Vbot\Contact\Groups;
use Hanson\Vbot\Message\Text;
use Hanson\Vbot\Support\File;
use Illuminate\Support\Collection;

class TextType
{
    public static function messageHandler(Collection $message, Friends $friends, Groups $groups)
    {
        if ($message['type'] === 'text') {
            if ($message['content'] === '拉我') {
                $username = $groups->getUsernameByNickname('Vbot 体验群');
                $groups->addMember($username, $message['from']['UserName']);
            }

            if ($message['content'] === '叫我') {
                $username = $friends->getUsernameByNickname('HanSon');
                Text::send($username, '主人');
            }

            if ($message['content'] === '头像') {
                $avatar = $friends->getAvatar($message['from']['UserName']);
                File::saveTo(vbot('config')['user_path'].'avatar/'.$message['from']['UserName'].'.jpg', $avatar);
            }

            if ($message['content'] === '报名') {
                $username = $groups->getUsernameByNickname('vbot 反馈群');
                $groups->addMember($username, $message['from']['UserName']);
            }

            if($message['fromType'] === 'Friend'){
                Text::send($message['from']['UserName'], static::reply($message['content'], $message['from']['UserName']));
            }

            if($message['fromType'] === 'Group' && $message['isAt']){
                Text::send($message['from']['UserName'], static::reply($message['pure'], $message['from']['UserName']));
            }
        }
    }

    private static function reply($content, $id)
    {
        try{
            $result = vbot('http')->post('http://www.tuling123.com/openapi/api', [
                'key'  => '1dce02aef026258eff69635a06b0ab7d',
                'info' => $content,
                'userid' => $id
            ], true);
            return isset($result['url']) ? $result['text'].$result['url'] : $result['text'];
        }catch (\Exception $e){
            return '图灵API连不上了，再问问试试';
        }
    }
}
