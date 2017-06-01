<?php

namespace Hanson\Vbot\Core;

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Card;
use Hanson\Vbot\Message\Emoticon;
use Hanson\Vbot\Message\GroupChange;
use Hanson\Vbot\Message\Image;
use Hanson\Vbot\Message\Location;
use Hanson\Vbot\Message\NewFriend;
use Hanson\Vbot\Message\Recall;
use Hanson\Vbot\Message\RedPacket;
use Hanson\Vbot\Message\RequestFriend;
use Hanson\Vbot\Message\Text;
use Hanson\Vbot\Message\Touch;
use Hanson\Vbot\Message\Transfer;
use Hanson\Vbot\Message\Video;
use Hanson\Vbot\Message\Voice;
use Illuminate\Support\Collection;

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
     * @return Collection
     */
    public function make($msg)
    {
        switch ($msg['MsgType']) {
            case 1: //文本消息
                if (Location::isLocation($msg)) {
                    return (new Location())->make($msg);
                } elseif ($this->vbot->friends->get($msg['FromUserName']) && str_contains($msg['Content'], '过了你的朋友验证请求')) {
                    return (new NewFriend())->make($msg);
                } else {
                    return (new Text())->make($msg);
                }
            case 3: // 图片消息
                return (new Image())->make($msg);
            case 34: // 语音消息
                return (new Voice())->make($msg);
            case 43: // 视频
                return (new Video())->make($msg);
            case 47: // 动画表情
                return (new Emoticon())->make($msg);
            case 10002:
                return (new Recall())->make($msg);
            case 10000:
                if (str_contains($msg['Content'], '利是') || str_contains($msg['Content'], '红包')) {
                    return (new RedPacket())->make($msg);
                } elseif (str_contains($msg['Content'], '添加') || str_contains($msg['Content'], '打招呼')) {
                    // 添加好友
                    return (new NewFriend())->make($msg);
                } elseif (str_contains($msg['Content'], '加入了群聊') || str_contains($msg['Content'], '移出了群聊') || str_contains($msg['Content'], '改群名为') || str_contains($msg['Content'], '移出群聊') || str_contains($msg['Content'], '邀请你') || str_contains($msg['Content'], '分享的二维码加入群聊')) {
                    return (new GroupChange())->make($msg);
                }
                break;
            case 49:
                if ($msg['FileName'] === '微信转账') {
                    return (new Transfer())->make($msg);
                } elseif ($msg['FileName'] === '我发起了位置共享') {
                    return (new Location())->make($msg);
                } elseif (str_contains($msg['Content'], '该类型暂不支持，请在手机上查看')) {
                    return;
                } else {
                    return $this->vbot->shareFactory->make($msg);
                }
            case 37: // 好友验证
                return (new RequestFriend())->make($msg);
            case 42: //共享名片
                return (new Card())->make($msg);
//            case 62:
//                //Video
//                break;
            case 51:
                if ($msg['ToUserName'] === $msg['StatusNotifyUserName']) {
                    return (new Touch())->make($msg);
                }
                break;
//            case 53:
//                //VideoCall
//                break;
            default:
                //Unknown
                break;
        }
    }
}
