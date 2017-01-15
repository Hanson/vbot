<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/15
 * Time: 0:12
 */

namespace Hanson\Robot\Message\Entity;


use Carbon\Carbon;
use Hanson\Robot\Core\Server;
use Hanson\Robot\Collections\Contact;
use Hanson\Robot\Collections\Official;
use Hanson\Robot\Collections\SpecialAccount;
use Hanson\Robot\Support\FileManager;
use Hanson\Robot\Support\Console;
use Hanson\Robot\Support\ObjectAble;

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

    public $msg;

    static $mediaCount = -1;

    public function __construct($msg)
    {
        $this->msg = $msg;

        $this->setFrom();
        $this->setTo();
        $this->setFromType();

        $this->msg['Content'] = html_entity_decode($this->formatContent($this->msg['Content']));
        if($this->fromType === 'Group'){
            $this->handleGroupContent($this->msg['Content']);
        }

        $this->time = $msg['CreateTime'];
    }

    /**
     * 设置消息发送者
     */
    private function setFrom()
    {
        $this->from = account()->getAccount($this->msg['FromUserName']);
    }

    private function setTo()
    {
        $this->to = account()->getAccount($this->msg['ToUserName']);
    }

    private function setFromType()
    {
        if ($this->msg['MsgType'] == 51) {
            $this->fromType = 'System';
        } elseif ($this->msg['MsgType'] == 37) {
            $this->fromType = 'FriendRequest';
        } elseif ($this->msg['FromUserName'] === myself()->username) {
            $this->fromType = 'Self';
        } elseif ($this->msg['ToUserName'] === 'filehelper') {
            $this->fromType = 'FileHelper';
        } elseif (substr($this->msg['FromUserName'], 0, 2) === '@@') { # group
            $this->fromType = 'Group';
        } elseif (contact()->getContactByUsername($this->msg['FromUserName'])) {
            $this->fromType = 'Contact';
        } elseif (Official::getInstance()->get($this->msg['FromUserName'])) {
            $this->fromType = 'Official';
        } elseif (SpecialAccount::getInstance()->get($this->msg['FromUserName'], false)) {
            $this->fromType = 'Special';
        } else {
            $this->fromType = 'Unknown';
        }
    }

    /**
     * 处理群发消息的内容
     *
     * @param $content string 内容
     */
    private function handleGroupContent($content)
    {
        if(!$content || !str_contains($content, ":\n")){
            return;
        }
        list($uid, $content) = explode(":\n", $content, 2);

        $this->sender = account()->getAccount($uid);
        $this->msg['Content'] = $this->formatContent($content);
        $this->isAt = str_contains($this->msg['Content'], '@'.myself()->nickname);
    }

    protected function formatContent($content)
    {
        return str_replace('<br/>', "\n", $content);
    }

    /**
     * 存储消息到 Message 集合
     */
    public function addMessageCollection()
    {
        message()->put($this->msg['MsgId'], [
            'content' => $this->content,
            'username' => $this->username,
            'sender' => $this->sender,
            'msg_type' => $this->msg['MsgType'],
            'type' => $this->type,
            'created_at' => $this->msg['CreateTime'],
            'from_type' => $this->fromType
        ]);
    }

    public function __toString()
    {
        return $this->content;
    }

}