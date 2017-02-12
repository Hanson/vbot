<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2017/1/14
 * Time: 11:54
 */

namespace Hanson\Vbot\Core;


use Hanson\Vbot\Message\Entity\Emoticon;
use Hanson\Vbot\Message\Entity\Image;
use Hanson\Vbot\Message\Entity\Location;
use Hanson\Vbot\Message\Entity\Message;
use Hanson\Vbot\Message\Entity\NewFriend;
use Hanson\Vbot\Message\Entity\GroupChange;
use Hanson\Vbot\Message\Entity\Recall;
use Hanson\Vbot\Message\Entity\Recommend;
use Hanson\Vbot\Message\Entity\RedPacket;
use Hanson\Vbot\Message\Entity\RequestFriend;
use Hanson\Vbot\Message\Entity\Text;
use Hanson\Vbot\Message\Entity\Touch;
use Hanson\Vbot\Message\Entity\Transfer;
use Hanson\Vbot\Message\Entity\Video;
use Hanson\Vbot\Message\Entity\Voice;
use Hanson\Vbot\Message\ShareFactory;

class MessageFactory
{

    public function make($msg)
    {
        return $this->handleMessageByType($msg);
    }


    /**
     * 处理消息类型
     * @param $msg
     * @return Message
     */
    private function handleMessageByType($msg)
    {
        switch($msg['MsgType']){
            case 1: //文本消息
                if(Location::isLocation($msg)){
                    return new Location($msg);
                }elseif(contact()->get($msg['FromUserName']) && str_contains($msg['Content'], '过了你的朋友验证请求')){
                    return new NewFriend($msg);
                }else{
                    return new Text($msg);
                }
            case 3: // 图片消息
                return new Image($msg);
            case 34: // 语音消息
                return new Voice($msg);
            case 43: // 视频
                return new Video($msg);
            case 47: // 动画表情
                return new Emoticon($msg);
            case 10002:
                return new Recall($msg);
            case 10000:
                if(str_contains($msg['Content'], '利是') || str_contains($msg['Content'], '红包') || str_contains($msg['Content'], 'Red Packet')){
                    return new RedPacket($msg);
                }
                else if(str_contains($msg['Content'], '添加') || str_contains($msg['Content'], 'have added') || str_contains($msg['Content'], '打招呼')){
                    # 添加好友
                    return new NewFriend($msg);
                }else if(str_contains($msg['Content'], '加入了群聊') || str_contains($msg['Content'], '移出了群聊') || str_contains($msg['Content'], '改群名为')){
                    return new GroupChange($msg);
                }
                break;
            case 49:
                if($msg['Status'] == 3 && $msg['FileName'] === '微信转账'){
                    return new Transfer($msg);
                }else{
                    return (new ShareFactory())->make($msg);
                }
            case 37: // 好友验证
                return new RequestFriend($msg);
            case 42: //共享名片
                return new Recommend($msg);
            case 62:
                //Video
                break;
            case 51:
                if($msg['ToUserName'] === $msg['StatusNotifyUserName']){
                    return new Touch($msg);
                }
                break;
            case 53:
                //VideoCall
                break;
            default:
                //Unknown
                break;
        }
        return null;
    }
}