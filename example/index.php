<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2016/12/7
 * Time: 16:33
 */

require_once __DIR__ . './../vendor/autoload.php';

use Hanson\Vbot\Foundation\Robot;
use Hanson\Vbot\Message\Entity\Message;
use Hanson\Vbot\Message\Entity\Image;
use Hanson\Vbot\Message\Entity\Text;
use Hanson\Vbot\Message\Entity\Emoticon;
use Hanson\Vbot\Message\Entity\Location;
use Hanson\Vbot\Message\Entity\Video;
use Hanson\Vbot\Message\Entity\Voice;
use Hanson\Vbot\Message\Entity\Recall;
use Hanson\Vbot\Message\Entity\RedPacket;
use Hanson\Vbot\Message\Entity\Transfer;
use Hanson\Vbot\Message\Entity\Recommend;
use Hanson\Vbot\Message\Entity\Share;
use Hanson\Vbot\Message\Entity\Touch;
use Hanson\Vbot\Message\Entity\RequestFriend;

$path = __DIR__ . '/./../tmp/';
$robot = new Robot([
    'tmp' => $path,
    'debug' => true
]);

$robot->server->setMessageHandler(function ($message) use ($path) {
    /** @var $message Message */

    // 位置信息 返回位置文字
    if ($message instanceof Location) {
        return $message;
    }

    // 文字信息
    if ($message instanceof Text) {
        // 联系人自动回复
        print_r($message);
        if ($message->fromType === 'Contact') {

            return http()->post('http://www.tuling123.com/openapi/api', [
                'key' => '1dce02aef026258eff69635a06b0ab7d',
                'info' => $message->content
            ], true)['text'];
            // 群组@我回复
        } elseif ($message->fromType === 'Group' && $message->isAt) {
            return http()->post('http://www.tuling123.com/openapi/api', [
                'key' => '1dce02aef026258eff69635a06b0ab7d',
                'info' => $message->content
            ], true)['text'];
        }
    }

    // 图片信息 返回接收到的图片
//    if ($message instanceof Image) {
//        return $message;
//    }

    // 视频信息 返回接收到的视频
//    if ($message instanceof Video) {
//        return $message;
//    }

    // 表情信息 返回接收到的表情
//    if ($message instanceof Emoticon) {
//        return $message;
//    }

    // 语音消息
//    if($message instanceof Voice){
//        /** @var $message Voice */
//        return '收到一条语音并下载在' . $message->getPath($message::$folder) . "/{$message->msg['MsgId']}.mp3";
//    }

    // 撤回信息
    if ($message instanceof Recall && $message->msg['FromUserName'] !== myself()->username) {
        /** @var $message Recall */
        if($message->origin instanceof Image){
            Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一张照片");
            Image::sendByMsgId($message->msg['FromUserName'], $message->origin->msg['MsgId']);
        }elseif($message->origin instanceof Emoticon){
            Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一个表情");
            Emoticon::sendByMsgId($message->msg['FromUserName'], $message->origin->msg['MsgId']);
        }elseif($message->origin instanceof Video){
            Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一个视频");
            Video::sendByMsgId($message->msg['FromUserName'], $message->origin->msg['MsgId']);
        }elseif($message->origin instanceof Voice){
            Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一条语音");
        }else{
            Text::send($message->msg['FromUserName'], "{$message->nickname} 撤回了一条信息 \"{$message->origin->msg['Content']}\"");
        }
//        return $message;
    }

    // 红包信息
    if($message instanceof RedPacket){
        // do something to notify if you want ...
        return $message->content . ' 来自 ' .$message->from['NickName'];
    }

    // 转账信息
    if($message instanceof Transfer){
        /** @var $message Transfer */
        return $message->content . ' 收到金额 ' . $message->fee;
    }

    // 推荐名片信息
    if($message instanceof Recommend){
        /** @var $message Recommend */
        if($message->isOfficial){
            return $message->from['NickName'] . ' 向你推荐了公众号 ' . $message->province . $message->city .
            " {$message->info['NickName']} 公众号信息： {$message->description}";
        }else{
            return $message->from['NickName'] . ' 向你推荐了 ' . $message->province . $message->city .
            " {$message->info['NickName']} 头像链接： {$message->bigAvatar}";
        }
    }

    // 请求添加信息
    if($message instanceof RequestFriend){
        /** @var $message RequestFriend */
        $groupUsername = group()->getGroupsByNickname('芬芬', true)->first()['UserName'];

        Text::send($groupUsername, "{$message->province}{$message->city} 的 {$message->info['NickName']} 请求添加好友 \"{$message->info['Content']}\"");

        if($message->info['Content'] === '上山打老虎'){
            Text::send($groupUsername, '暗号正确');
            $message->verifyUser($message::VIA);
        }else{
            Text::send($groupUsername, '暗号错误');
        }
    }

    // 分享信息
    if($message instanceof Share){
        /** @var $message Share */
        $reply = "收到分享\n标题：{$message->title}\n描述：{$message->description}\n链接：{$message->url}";
        if($message->app){
            $reply .= "\n来源APP：{$message->app}";
        }
        return $reply;
    }

    // 手机点击聊天事件
    if($message instanceof Touch){
        print_r($message);
        Text::send($message->to['UserName'], "我点击了此群");
    }

    return false;

});

$robot->server->run();
