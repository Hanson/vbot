<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/15
 * Time: 0:12
 */

namespace Hanson\Robot\Message;


use Carbon\Carbon;
use Hanson\Robot\Core\Server;
use Hanson\Robot\Collections\Contact;
use Hanson\Robot\Collections\OfficialAccount;
use Hanson\Robot\Collections\SpecialAccount;
use Hanson\Robot\Support\FileManager;
use Hanson\Robot\Support\Console;

class Message
{

    /**
     * @var array 消息来源
     */
    public $from;

    /**
     * @var array 当from为群组时，sender为用户发送者
     */
    public $sender;

    /**
     * @var string 来源的username，用于回复
     */
    public $username;

    /**
     * @var array 消息接收者
     */
    public $to;

    /**
     * @var string 经过处理的内容
     */
    public $content;

    /**
     * @var Carbon 时间
     */
    public $time;

    /**
     * @var string 消息发送者类型
     */
    public $fromType;

    /**
     * @var string 消息类型
     */
    public $type;

    public $isAt = false;

    const USER_TYPE = [
        0 => 'Init',
        1 => 'Self',
        2 => 'FileHelper',
        3 => 'Group',
        4 => 'Contact',
        5 => 'Public',
        6 => 'Special',
        99 => 'UnKnown',
    ];

    public $rawMsg;

    public function make($selector, $msg)
    {
        $this->rawMsg = $msg;
        $this->time = Carbon::now();

        $this->setFrom();
        $this->setTo();
        $this->setFromType();
        $this->setType();
        $this->rawMsg['selector'] = $selector;

        return $this;
    }

    /**
     * 设置消息发送者
     */
    private function setFrom()
    {
        $this->from = contact()->getContactByUsername($this->rawMsg['FromUserName']);
        $this->username = $this->rawMsg['FromUserName'];
    }

    private function setTo()
    {
        $this->to = contact()->getContactByUsername($this->rawMsg['ToUserName']);
    }

    private function setFromType()
    {
        if ($this->rawMsg['MsgType'] == 51) {
            $this->fromType = 'System';
        } elseif ($this->rawMsg['MsgType'] == 37) {
            $this->fromType = 'FriendRequest';
        } elseif ($this->rawMsg['FromUserName'] === myself()->userName) {
            $this->fromType = 'Self';
        } elseif ($this->rawMsg['ToUserName'] === 'filehelper') {
            $this->fromType = 'FileHelper';
        } elseif (substr($this->rawMsg['FromUserName'], 0, 2) === '@@') { # group
            $this->fromType = 'Group';
        } elseif (contact()->getContactByUsername($this->rawMsg['FromUserName'])) {
            $this->fromType = 'Contact';
        } elseif (OfficialAccount::getInstance()->isPublic($this->rawMsg['FromUserName'])) {
            $this->fromType = 'Official';
        } elseif (SpecialAccount::getInstance()->get($this->rawMsg['FromUserName'], false)) {
            $this->fromType = 'Special';
        } else {
            $this->fromType = 'Unknown';
        }
    }

    private function setType()
    {
        $this->rawMsg['Content'] = html_entity_decode($this->rawMsg['Content']);

        $this->setTypeByFrom();

        $this->handleMessageByType();
    }

    /**
     * 根据消息来源处理消息
     */
    private function setTypeByFrom()
    {
        if($this->fromType === 'System'){
            $this->type = 'Empty';
        }elseif ($this->fromType === 'FileHelper'){ # File Helper
            $this->type = 'Text';
            $this->content = $this->formatContent($this->rawMsg['Content']);
        }elseif ($this->fromType === 'Group'){
            $this->handleGroupContent($this->rawMsg['Content']);
        }
    }

    /**
     * 处理消息类型
     */
    private function handleMessageByType()
    {
        switch($this->rawMsg['MsgType']){
            case 1:
                if(Location::isLocation($this->rawMsg)){
                    $this->type = 'Location';
                    $this->content = Location::getLocationText($this->rawMsg['Content']);
                }else{
                    $this->type = 'Text';
                    $this->content = $this->rawMsg['Content'];
                }
                break;
            case 3:
                $this->type = 'Image';
                $this->content = Server::BASE_URI . sprintf('/webwxgetmsgimg?MsgID=%s&skey=%s', $this->rawMsg['MsgId'], server()->skey);
                $content = http()->get($this->content);
                FileManager::download(time().$this->rawMsg['MsgId'].'.jpg', $content, 'jpg');
                break;
            case 34:
                $this->type = 'Voice';
                $this->content = Server::BASE_URI . sprintf('/webwxgetvoice?msgid=%s&skey=%s', $this->rawMsg['MsgId'], server()->skey);
                $content = http()->get($this->content);
                FileManager::download(time().$this->rawMsg['MsgId'].'.mp3', $content, 'mp3');
                break;
            case 37:
                $this->type = 'AddUser';
                break;
            case 42:
                $this->type = 'Recommend';
                $this->content = (object)$this->rawMsg['RecommendInfo'];
                break;
            case 47:
                $this->type = 'Animation';
                break;
            case 49:
                $this->type = 'Share';
                break;
            case 62:
                $this->type = 'Video';
                break;
            case 53:
                $this->type = 'VideoCall';
                break;
            case 10002:
                $this->type = 'Redraw';
                break;
            case 10000:
                if($this->rawMsg['Status'] == 4){
                    $this->type = 'RedPacket'; // 红包
                }else{
                    $this->type = 'Unknown';
                }
                break;
            default:
                $this->type = 'Unknown';
                break;
        }
    }

    /**
     * 处理群发消息的内容
     *
     * @param $content string 内容
     */
    private function handleGroupContent($content)
    {
        if(!$content){
            return;
        }
        list($uid, $content) = explode('<br/>', $content, 2);

        $this->sender = member()->getMemberByUsername(substr($uid, 0, -1));
        $this->rawMsg['Content'] = $this->formatContent($content);
        $this->isAt = str_contains($this->rawMsg['Content'], '@'.myself()->nickname);
    }

    private function formatContent($content)
    {
        return str_replace('<br/>', "\n", $content);
    }

    /**
     * 发送消息
     *
     * @param $word string 消息内容
     * @param $username string 目标username
     * @return bool
     */
    public static function send($word, $username)
    {
        if(!$word && !is_string($word)){
            return false;
        }

        $random = strval(time() * 1000) . '0' . strval(rand(100, 999));

        $data = [
            'BaseRequest' => server()->baseRequest,
            'Msg' => [
                'Type' => 1,
                'Content' => $word,
                'FromUserName' => myself()->userName,
                'ToUserName' => $username,
                'LocalID' => $random,
                'ClientMsgId' => $random,
            ],
            'Scene' => 0
        ];
        $result = http()->post(Server::BASE_URI . '/webwxsendmsg?pass_ticket=' . server()->passTicket,
            json_encode($data, JSON_UNESCAPED_UNICODE), true
        );

        if($result['BaseResponse']['Ret'] != 0){
            Console::log('发送消息失败');
            return false;
        }

        return true;
    }

}