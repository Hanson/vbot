<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2017/1/14
 * Time: 11:54
 */

namespace Hanson\Robot\Core;


use Hanson\Robot\Message\Entity\Emoticon;
use Hanson\Robot\Message\Entity\Image;
use Hanson\Robot\Message\Entity\Location;
use Hanson\Robot\Message\Entity\Recall;
use Hanson\Robot\Message\Entity\Recommend;
use Hanson\Robot\Message\Entity\RedPacket;
use Hanson\Robot\Message\Entity\RequestFriend;
use Hanson\Robot\Message\Entity\Share;
use Hanson\Robot\Message\Entity\Text;
use Hanson\Robot\Message\Entity\Touch;
use Hanson\Robot\Message\Entity\Transfer;
use Hanson\Robot\Message\Entity\Video;
use Hanson\Robot\Message\Entity\Voice;
use Hanson\Robot\Support\Console;

class MessageFactory
{

    public $msg;

    public function make($selector, $msg)
    {
        $this->msg = $msg;
        
        return $this->handleMessageByType();
    }


    /**
     * 处理消息类型
     *
     */
    private function handleMessageByType()
    {
        Console::log($this->msg['MsgType']);
        switch($this->msg['MsgType']){
            case 1: //文本消息
                if(Location::isLocation($this->msg)){
                    return new Location($this->msg);
                }else{
                    return new Text($this->msg);
                }
            case 3: // 图片消息
                return new Image($this->msg);
            case 34: // 语音消息
                return new Voice($this->msg);
            case 43: // 视频
                return new Video($this->msg);
            case 47: // 动画表情
                return new Emoticon($this->msg);
            case 10002:
                return new Recall($this->msg);
            case 10000:
                if($this->msg['Status'] == 4){
                    return new RedPacket($this->msg);
                }else{
                }
                break;
            case 49:
                if($this->msg['Status'] == 3 && $this->msg['FileName'] === '微信转账'){
                    return new Transfer($this->msg);
                }else{
                    return new Share($this->msg);
                }
            case 37: // 好友验证
                return new RequestFriend($this->msg);
            case 42: //共享名片
                return new Recommend($this->msg);
            case 62:
                $this->type = 'Video';
                break;
            case 51:
                if($this->msg['ToUserName'] === $this->msg['StatusNotifyUserName']){
                    return new Touch($this->msg);
                }
                break;
            case 53:
                $this->type = 'VideoCall';
                break;
            default:
                $this->type = 'Unknown';
                break;
        }
    }
}