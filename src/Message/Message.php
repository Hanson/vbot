<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/15
 * Time: 0:12
 */

namespace Hanson\Robot\Message;


use Hanson\Robot\Core\Server;
use Hanson\Robot\Models\Account;
use Hanson\Robot\Models\ContactAccount;
use Hanson\Robot\Models\OfficialAccount;
use Hanson\Robot\Models\SpecialAccount;

class Message
{

    /**
     * @
     */
    public $sender;

    public $receiver;

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

        if ($msg['MsgType'] == 51) {
            $this->sender->name = 'system';
            $this->sender->type = 0;
        } elseif ($msg['MsgType'] == 37) {
            $this->sender->type = 37;
        } elseif (Server::isMyself($msg['FromUserName'])) {
            $this->sender->name = 'self';
            $this->sender->type = 1;
        } elseif ($msg['ToUserName'] === 'filehelper') {
            $this->sender->name = 'file_helper';
            $this->sender->type = 2;
        } elseif (substr($msg['FromUserName'], 0, 2) === '@@') {
            $this->sender->name = Account::getInstance()->getContactName($msg['FromUserName'], Account::NORMAL_MEMBER, true);
            $this->sender->type = 3;
        } elseif (ContactAccount::getInstance()->isContact($msg['FromUserName'])) {
            $this->sender->name = Account::getInstance()->getContactName($msg['FromUserName'], Account::NORMAL_MEMBER, true);
            $this->sender->type = 4;
        } elseif (OfficialAccount::getInstance()->isPublic($msg['FromUserName'])) {
            $this->sender->name = Account::getInstance()->getContactName($msg['FromUserName'], Account::NORMAL_MEMBER, true);
            $this->sender->type = 5;
        } elseif (SpecialAccount::getInstance()->get($msg['FromUserName'], false)) {
            $this->sender->name = Account::getInstance()->getContactName($msg['FromUserName'], Account::NORMAL_MEMBER, true);
            $this->sender->type = 6;
        } else {
            $this->sender->name = 'unknown';
            $this->sender->type = 99;
        }

        $this->sender->name = html_entity_decode($this->sender->name);

        $this->handleContent();
    }

    private function handleContent()
    {
//        $msgType = $msg['MsgType'];
        $content = html_entity_decode($this->rawMsg['Content']);
//        $msgId = $msg['MsgId'];

        $this->handleContentByType($content);

        $this->handleMessageByType();
    }

    private function handleContentByType($content)
    {
        if($this->sender->type === 0){
            $this->type = 'Empty';
        }elseif ($this->sender->type === 2){
            $this->type = 'Text';
            $this->content = $this->formatContent($content);
        }elseif ($this->sender->type === 3){
            $this->handleGroupContent($content);
        }
    }

    private function handleMessageByType()
    {
        if($this->rawMsg['MsgType'] == 1){

        }
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
        $this->content = $this->formatContent($content);
    }

    private function formatContent($content)
    {
        return str_replace('<br/>', '\n', $content);
    }

}