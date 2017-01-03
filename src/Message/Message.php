<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/15
 * Time: 0:12
 */

namespace Hanson\Robot\Message;


use Hanson\Robot\Core\Server;
use Hanson\Robot\Collections\Account;
use Hanson\Robot\Collections\ContactAccount;
use Hanson\Robot\Collections\OfficialAccount;
use Hanson\Robot\Collections\SpecialAccount;
use Hanson\Robot\Models\Content;
use Hanson\Robot\Models\Sender;
use Hanson\Robot\Support\FileManager;
use Hanson\Robot\Support\Console;

class Message
{

    public $from;

    /**
     * @var array 当from为群组时，sender为用户发送者
     */
    public $sender;

    public $to;

    public $content;

    public $time;

    /**
     * @var string 消息发送者类型
     */
    public $FromType;

    /**
     * @var string 消息类型
     */
    public $type;

    static $message = [];

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

//    const MESSAGE_TYPE = [
//        0 => 'Text',
//    ]

    public function make($selector, $msg)
    {
        $this->rawMsg = $msg;

        $this->setFrom();

        $this->setTo();

        $this->setFromType();

        $this->setType();

        return $this;
    }

    /**
     * 设置消息发送者
     */
    private function setFrom()
    {
        $this->from = Account::getInstance()->getContact($this->rawMsg['FromUserName']);
    }

    private function setTo()
    {
        $this->to = Account::getInstance()->getContact($this->rawMsg['ToUserName']);
    }

    private function setFromType()
    {
        if ($this->rawMsg['MsgType'] == 51) {
            $this->FromType = 'System';
        } elseif ($this->rawMsg['MsgType'] == 37) {
            $this->FromType = 'FriendRequest';
        } elseif ($this->rawMsg['FromUserName'] === myself()->userName) {
            $this->FromType = 'Self';
        } elseif ($this->rawMsg['ToUserName'] === 'filehelper') {
            $this->FromType = 'FileHelper';
        } elseif (substr($this->rawMsg['FromUserName'], 0, 2) === '@@') { # group
            $this->FromType = 'Group';
        } elseif (ContactAccount::getInstance()->isContact($this->rawMsg['FromUserName'])) {
            $this->FromType = 'Contact';
        } elseif (OfficialAccount::getInstance()->isPublic($this->rawMsg['FromUserName'])) {
            $this->FromType = 'Public';
        } elseif (SpecialAccount::getInstance()->get($this->rawMsg['FromUserName'], false)) {
            $this->FromType = 'Special';
        } else {
            $this->FromType = 'Unknown';
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
        if($this->FromType === 'System'){
            $this->type = 'Empty';
        }elseif ($this->FromType === 'FileHelper'){ # File Helper
            $this->type = 'Text';
            $this->content->msg = $this->formatContent($this->rawMsg['Content']);
        }elseif ($this->FromType === 'Group'){
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
                if(Location::isLocation($this->rawMsg['Content'])){
                    $this->type = 'Location';
                }else{
                    $this->type = 'Text';
                    $this->content = $this->rawMsg['Content'];
                }
                break;
            case 3:
                $this->type = 'Image';
                $this->content = Server::BASE_URI . sprintf('/webwxgetmsgimg?MsgID=%s&skey=%s', $this->rawMsg['MsgId'], server()->skey);
                $content = http()->get($this->content);
                FileManager::download(server()->config['tmp'] . $this->rawMsg['MsgId'] . '.jpg', $content);
                break;
            case 34:
                $this->type = 'Voice';
                $this->content = Server::BASE_URI . sprintf('/webwxgetvoice?msgid=%s&skey=%s', $this->rawMsg['MsgId'], server()->skey);
                $content = http()->get($this->content);
                FileManager::download(server()->config['tmp'] . $this->rawMsg['MsgId'] . '.mp3', $content);
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
                $this->type = 'Unknown';
                break;
            default:
                $this->type = 'Unknown';
                break;


        }
    }

    /**
     * 设置当前message 为 location
     */
    private function setLocationMessage()
    {
        $this->FromType = 'Location';
//        $this->url = $this->rawMsg['Url'];
        $this->content->msg = Location::getLocationText($this->rawMsg['Content']);
    }

    /**
     * handle group content
     *
     * @param $content
     */
    private function handleGroupContent($content)
    {
        list($uid, $content) = explode('<br/>', $content, 2);

        $this->sender = Account::getInstance()->getGroupMember(substr($uid, 0, -1));
        $this->rawMsg['Content'] = $this->formatContent($content);
    }

    private function formatContent($content)
    {
        return str_replace('<br/>', '\n', $content);
    }

    /**
     * 发送消息
     *
     * @param $word string 消息内容
     * @param $fromUser string 目标username
     * @return bool
     */
    public static function send($word, $fromUser)
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
                'ToUserName' => $fromUser,
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