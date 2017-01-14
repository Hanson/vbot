<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2017/1/14
 * Time: 11:54
 */

namespace Hanson\Robot\Core;


use Hanson\Robot\Message\Location;
use Hanson\Robot\Support\Console;
use Hanson\Robot\Support\FileManager;

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
        switch($this->msg['MsgType']){
            case 1: //文本消息
                if(Location::isLocation($this->msg)){
                    return new Location($this->msg);
                }else{
                    $this->type = 'Text';
                    $this->content = $this->msg['Content'];
                }
                break;
            case 3: // 图片消息
                $this->type = 'Image';
                $this->content = Server::BASE_URI . sprintf('/webwxgetmsgimg?MsgID=%s&skey=%s', $this->msg['MsgId'], server()->skey);
                $content = http()->get($this->content);
                FileManager::download($this->msg['MsgId'].'.jpg', $content, 'jpg');
                break;
            case 34: // 语音消息
                $this->type = 'Voice';
                $this->content = Server::BASE_URI . sprintf('/webwxgetvoice?msgid=%s&skey=%s', $this->msg['MsgId'], server()->skey);
                $content = http()->get($this->content);
                FileManager::download($this->msg['MsgId'].'.mp3', $content, 'mp3');
                break;
            case 37: // 好友验证
                $this->type = 'AddUser';
                break;
            case 42: //共享名片
                $this->type = 'Recommend';
                $this->content = (object)$this->msg['RecommendInfo'];
                break;
            case 43:
                $this->type = 'VideoCall';
                Console::log('video');
                $url = Server::BASE_URI . sprintf('/webwxgetvideo?msgid=%s&skey=%s', $this->msg['MsgId'], server()->skey);
                $content = http()->request($url, 'get', [
                    'headers' => [
                        'Range' => 'bytes=0-'
                    ]
                ]);
                FileManager::download($this->msg['MsgId'].'.mp4', $content, 'mp4');
                break;
            case 47: // 动画表情
                $this->type = 'Emoticon';
                $this->content = Server::BASE_URI . sprintf('/webwxgetmsgimg?MsgID=%s&skey=%s', $this->msg['MsgId'], server()->skey);
                $content = http()->get($this->content);
                FileManager::download($this->msg['MsgId'].'.gif', $content, 'gif');
                break;
            case 49:
                $this->type = 'Share';
                break;
            case 62:
                $this->type = 'Video';
                Console::log('video');
                $url = Server::BASE_URI . sprintf('/webwxgetvideo?msgid=%s&skey=%s', $this->msg['MsgId'], server()->skey);
                $content = http()->request($url, 'get', [
                    'headers' => [
                        'Range' => 'bytes=0-'
                    ]
                ]);
                FileManager::download($this->msg['MsgId'].'.mp4', $content, 'mp4');
                break;
            case 51:
                $this->type = 'Init';
                break;
            case 53:
                $this->type = 'VideoCall';
                break;
            case 10000:
                if($this->msg['Status'] == 4){
                    $this->type = 'RedPacket'; // 红包
                }else{
                    $this->type = 'Unknown';
                }
                break;
            case 10002:
                $this->type = 'Recall'; // 撤回
                $this->msgId = $msgId = $this->parseMsgId($this->msg['Content']);
                break;
            default:
                $this->type = 'Unknown';
                break;
        }
    }
}