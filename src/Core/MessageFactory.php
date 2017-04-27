<?php

namespace Hanson\Vbot\Core;

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Emoticon;
use Hanson\Vbot\Message\GroupChange;
use Hanson\Vbot\Message\Image;
use Hanson\Vbot\Message\Location;
use Hanson\Vbot\Message\Message;
use Hanson\Vbot\Message\NewFriend;
use Hanson\Vbot\Message\Recall;
use Hanson\Vbot\Message\Recommend;
use Hanson\Vbot\Message\RedPacket;
use Hanson\Vbot\Message\RequestFriend;
use Hanson\Vbot\Message\Text;
use Hanson\Vbot\Message\Touch;
use Hanson\Vbot\Message\Transfer;
use Hanson\Vbot\Message\Video;
use Hanson\Vbot\Message\Voice;

class MessageFactory
{
    /**
     * @var Vbot
     */
    private $vbot;

    /**
     * MessageFactory constructor.
     *
     * @param Vbot $vbot
     */
    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    /**
     * @param $msg
     *
     * @return Message
     */
    public function make($msg)
    {
        switch ($msg['MsgType']) {
            case 1: //文本消息
                if (Location::isLocation($msg)) {
                    //                    return new Location($this->vbot, $msg);
                } elseif ($this->vbot->friends->get($msg['FromUserName']) && str_contains($msg['Content'], '过了你的朋友验证请求')) {
                    //                    return new NewFriend($this->vbot, $msg);
                } else {
                    return new Text($this->vbot, $msg);
                }
//            case 3: // 图片消息
//                return new Image($this->vbot, $msg);
//            case 34: // 语音消息
//                return new Voice($this->vbot, $msg);
//            case 43: // 视频
//                return new Video($this->vbot, $msg);
//            case 47: // 动画表情
//                return new Emoticon($this->vbot, $msg);
//            case 10002:
//                return new Recall($this->vbot, $msg);
//            case 10000:
//                if (str_contains($msg['Content'], '利是') || str_contains($msg['Content'], '红包')) {
//                    return new RedPacket($this->vbot, $msg);
//                } elseif (str_contains($msg['Content'], '添加') || str_contains($msg['Content'], '打招呼')) {
//                    // 添加好友
//                    return new NewFriend($this->vbot, $msg);
//                } elseif (str_contains($msg['Content'], '加入了群聊') || str_contains($msg['Content'], '移出了群聊') || str_contains($msg['Content'], '改群名为') || str_contains($msg['Content'], '移出群聊') || str_contains($msg['Content'], '邀请你') || str_contains($msg['Content'], '分享的二维码加入群聊')) {
//                    return new GroupChange($this->vbot, $msg);
//                }
//                break;
//            case 49:
//                if ($msg['Status'] == 3 && $msg['FileName'] === '微信转账') {
//                    return new Transfer($this->vbot, $msg);
//                } elseif ($msg['Content'] === '该类型暂不支持，请在手机上查看') {
//                    return;
//                } else {
//                    return $this->vbot->shareFactory->make($this->vbot, $msg);
//                }
//            case 37: // 好友验证
//                return new RequestFriend($this->vbot, $msg);
//            case 42: //共享名片
//                return new Recommend($this->vbot, $msg);
//            case 62:
//                //Video
//                break;
//            case 51:
//                if ($msg['ToUserName'] === $msg['StatusNotifyUserName']) {
//                    return new Touch($this->vbot, $msg);
//                }
//                break;
//            case 53:
//                //VideoCall
//                break;
            default:
                //Unknown
                break;
        }
    }
}
