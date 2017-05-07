<?php

namespace Hanson\Vbot\Example\Modules;

use Hanson\Vbot\Contact\Friends;
use Hanson\Vbot\Contact\Groups;
use Hanson\Vbot\Contact\Members;
use Hanson\Vbot\Message\Card;
use Hanson\Vbot\Message\Emoticon;
use Hanson\Vbot\Message\File;
use Hanson\Vbot\Message\Image;
use Hanson\Vbot\Message\Text;
use Hanson\Vbot\Message\Video;
use Hanson\Vbot\Message\Voice;
use Illuminate\Support\Collection;

class MessageModule
{
    public static function messageHandler(Collection $message)
    {
        /** @var Friends $friends */
        $friends = vbot('friends');

        /** @var Members $members */
        $members = vbot('members');

        /** @var Groups $groups */
        $groups = vbot('groups');

        if ($message['from']['NickName'] === 'HanSon') {
            if ($message['type'] === 'text' && $message['content'] === 'hi') {
                Text::send($message['from']['UserName'], 'hi');
            }

            if ($message['type'] === 'location') {
                Text::send($message['from']['UserName'], $message['content']);
                Text::send($message['from']['UserName'], $message['url']);
            }

            if ($message['type'] === 'new_friend') {
                Text::send($message['from']['UserName'], $message['content']);
            }

            if ($message['type'] === 'image') {
                Image::download($message);
                Image::download($message, function ($resource) {
                    file_put_contents(__DIR__.'/test1.jpg', $resource);
                });
                Image::send($message['from']['UserName'], $message);
//                Image::send($message['from']['UserName'], __DIR__.'/test1.jpg');
            }

            if ($message['type'] === 'voice') {
                //                Voice::download($message);
//                Voice::download($message, function ($resource) {
//                    file_put_contents(__DIR__.'/test1.mp3', $resource);
//                });
                Voice::send($message['from']['UserName'], $message);
//                Voice::send($message['from']['UserName'], __DIR__.'/test1.mp3');
            }

            if ($message['type'] === 'video') {
                //                Video::download($message);
//                Video::download($message, function($resource){
//                    file_put_contents(__DIR__.'/test1.mp4', $resource);
//                });
                Video::send($message['from']['UserName'], $message);
//                Video::send($message['from']['UserName'], __DIR__.'/test1.mp4');
            }

            if ($message['type'] === 'emoticon') {
                //                Emoticon::download($message);
//                Video::download($message, function($resource){
//                    file_put_contents(__DIR__.'/test1.mp4', $resource);
//                });
                Emoticon::send($message['from']['UserName'], $message);
                Emoticon::sendRandom($message['from']['UserName']);
            }

            if ($message['type'] === 'recall') {
                Text::send($message['from']['UserName'], $message['content']);
                Text::send($message['from']['UserName'], $message['origin']['content']);
            }

            if ($message['type'] === 'red_packet') {
                Text::send($message['from']['UserName'], $message['content']);
            }

            if($message['type'] === 'transfer'){
                Text::send($message['from']['UserName'], $message['content'].' 转账金额： '.$message['fee'] .
                    ' 转账流水号：' . $message['transaction_id'] . ' 备注：' . $message['memo']);
            }

            if($message['type'] === 'file'){
                File::send($message['from']['UserName'], $message);
                Text::send($message['from']['UserName'], '收到文件：' . $message['title']);
            }

            if($message['type'] === 'mina'){
                Text::send($message['from']['UserName'], '收到小程序：' . $message['title'] . $message['url']);
            }

            if($message['type'] === 'share'){
                Text::send($message['from']['UserName'], '收到分享:' . $message['title'] . $message['description'] .
                    $message['app'] . $message['url']);
            }

            if($message['type'] === 'card'){
                Text::send($message['from']['UserName'], '收到名片:' . $message['avatar'] . $message['province'] .
                    $message['city'] . $message['description']);
            }

        }

        if($message['type'] === 'touch'){
            Text::send($message['raw']['ToUserName'], $message['content']);
        }

        if ($message['from']['NickName'] === 'Vbot 体验群') {

            if($message['type'] === 'group_change'){
                Text::send($message['from']['UserName'], '此次操作为 '.$message['action']);
            }

            if($message['type'] === 'text'){

                if($message['sender']['NickName'] === 'HanSon'){
                    if(str_contains($message['content'], '加人')){
                        $username = str_replace('加人', '', $message['content']);
                        $friends->add($username, '我是你儿子');
                    }
                }

                if(str_contains($message['content'], '搜人')){
                    $nickname = str_replace('搜人', '', $message['content']);
                    $members = $groups->getMembersByNickname($message['from']['UserName'], $nickname, true);
                    $result = '搜索结果 数量：'.count($members)."\n";
                    foreach ($members as $member) {
                        $result .= $member['NickName'].' '.$member['UserName']."\n";
                    }
                    Text::send($message['from']['UserName'], $result);
                }

                if($message['content'] === '名片'){
                    Card::send($message['from']['UserName'], 'hanson1994', 'HanSon大人');
                }

                if($message['content'] === '公众号'){
                    Card::send($message['from']['UserName'], 'hgjxxg', '华广计协小哥');
                }
            }

        }

        // @todo
        if($message['type'] === 'official'){
            vbot('console')->log('收到公众号消息:' . $message['title'] . $message['description'] .
                $message['app'] . $message['url']);
        }

        if($message['type'] === 'request_friend'){
            vbot('console')->log('收到好友申请:' . $message['info']['Content'] . $message['avatar']);
            if($message['info']['Content'] === 'echo'){
                $friends->approve($message);
            }
        }
    }
}
