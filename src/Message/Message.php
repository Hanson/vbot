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

class Message
{

    /**
     * @var Sender
     */
    public $from;

    public $to;

    /**
     * @var Content
     */
    public $content;

    public $time;

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

//        $this->sender = new Sender();
//        $this->content = new Content();

        $this->setSender();

        $this->setContent();

        return $this;
    }

    /**
     * 设置消息发送者
     */
    private function setSender()
    {
        $account = Account::getInstance();

        $from = $this->rawMsg['FromUserName'];

        $fromType = substr($this->rawMsg['FromUserName'], 0, 2) === '@@' ? Account::GROUP_MEMBER : Account::NORMAL_MEMBER;

        $this->from = $account->getContact($from, $fromType);

//        if ($this->rawMsg['MsgType'] == 51) {
//            $this->sender->name = 'system';
//            $this->sender->type = 'System';
//        } elseif ($this->rawMsg['MsgType'] == 37) {
//            $this->sender->type = 'FriendRequest';
//        } elseif (Server::isMyself($this->rawMsg['FromUserName'])) {
//            $this->sender->name = 'self';
//            $this->sender->type = 'Self';
//        } elseif ($this->rawMsg['ToUserName'] === 'filehelper') {
//            $this->sender->name = 'file_helper';
//            $this->sender->type = 'FileHelper';
//        } elseif (substr($this->rawMsg['FromUserName'], 0, 2) === '@@') { # group
//            $this->sender->name = $account->getContactName($this->rawMsg['FromUserName'], Account::GROUP_MEMBER, true);
//            $this->sender->type = 'Group';
//            $this->sender->group = $account->getContact($this->rawMsg['FromUserName'], Account::GROUP_MEMBER);
//        } elseif (ContactAccount::getInstance()->isContact($this->rawMsg['FromUserName'])) {
//            $this->sender->name = $account->getContactName($this->rawMsg['FromUserName'], Account::NORMAL_MEMBER, true);
//            $this->sender->type = 'Contact';
//        } elseif (OfficialAccount::getInstance()->isPublic($this->rawMsg['FromUserName'])) {
//            $this->sender->name = $account->getContactName($this->rawMsg['FromUserName'], Account::NORMAL_MEMBER, true);
//            $this->sender->type = 'Public';
//        } elseif (SpecialAccount::getInstance()->get($this->rawMsg['FromUserName'], false)) {
//            $this->sender->name = $account->getContactName($this->rawMsg['FromUserName'], Account::NORMAL_MEMBER, true);
//            $this->sender->type = 'Special';
//        } else {
//            $this->sender->name = 'unknown';
//            $this->sender->type = 'Unknown';
//        }
//
//        if($this->sender->type !== 'Group'){
//            $this->sender->from = $account->getContact($this->rawMsg['FromUserName'], Account::NORMAL_MEMBER);
//        }
//
//        $this->sender->name = html_entity_decode($this->sender->name);
    }

    private function setContent()
    {
        $this->handleContent();
    }

    private function handleContent()
    {
//        $msgType = $msg['MsgType'];
        $this->rawMsg['Content'] = html_entity_decode($this->rawMsg['Content']);
//        $msgId = $msg['MsgId'];

        $this->handleContentByType();

        $this->handleMessageByType();
    }

    /**
     * 根据消息来源处理消息
     */
    private function handleContentByType()
    {
        if($this->sender->type === 'System'){
            $this->content->type = 'Empty';
        }elseif ($this->sender->type === 'FileHelper'){ # File Helper
            $this->content->type = 'Text';
            $this->content->msg = $this->formatContent($this->rawMsg['Content']);
        }elseif ($this->sender->type === 'Group'){ # group
            $this->handleGroupContent($this->rawMsg['Content']);
        }
    }

    /**
     * 处理消息类型
     */
    private function handleMessageByType()
    {
        if($this->rawMsg['MsgType'] == 1){
            if(Location::isLocation($this->rawMsg['Content'])){
                $this->setLocationMessage();
            }else{

            }
        }
    }

    /**
     * 设置当前message 为 location
     */
    private function setLocationMessage()
    {
        $this->type = 'Location';
        $this->url = $this->rawMsg['Url'];
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

        $this->sender->user = Account::getInstance()->get('normalMember')[substr($uid, 0, -1)];
        $this->content->msg = $this->formatContent($content);
    }

    private function formatContent($content)
    {
        return str_replace('<br/>', '\n', $content);
    }

}