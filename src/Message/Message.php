<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/15
 * Time: 0:12.
 */

namespace Hanson\Vbot\Message;

use Carbon\Carbon;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Support\Content;
use Illuminate\Support\Collection;

abstract class Message
{
    const FROM_TYPE_SYSTEM = 'System';
    const FROM_TYPE_SELF = 'Self';
    const FROM_TYPE_GROUP = 'Group';
    const FROM_TYPE_FRIEND = 'Friend';
    const FROM_TYPE_OFFICIAL = 'Official';
    const FROM_TYPE_SPECIAL = 'Special';
    const FROM_TYPE_UNKNOWN = 'Unknown';

    /**
     * @var array 消息来源
     */
    public $from;

    /**
     * @var array 当from为群组时，sender为用户发送者
     */
    public $sender = null;

    /**
     * 发送者 username.
     *
     * @var
     */
    public $username;

    /**
     * @var string 经处理的内容 （与类型无关 有可能是一串xml）
     */
    public $message;

    /**
     * @var Carbon 时间
     */
    public $time;

    /**
     * @var string 消息发送者类型
     */
    public $fromType;

    /**
     * @var array 原始数据
     */
    public $raw;

    protected function create($msg):array
    {
        $this->raw = $msg;

        $this->setFrom();
        $this->setFromType();
        $this->setMessage();
        $this->setTime();
        $this->setUsername();

        return ['raw' => $this->raw, 'from' => $this->from, 'fromType' => $this->fromType, 'sender' => $this->sender,
            'message' => $this->message, 'time' => $this->time, 'username' => $this->username, ];
    }

    /**
     * 设置消息发送者.
     */
    private function setFrom()
    {
        $this->from = vbot('contacts')->getAccount($this->raw['FromUserName']);
    }

    private function setFromType()
    {
        if ($this->raw['MsgType'] == 51) {
            $this->fromType = self::FROM_TYPE_SYSTEM;
        } elseif ($this->raw['FromUserName'] === vbot('myself')->username) {
            $this->fromType = self::FROM_TYPE_SELF;
            $this->from = vbot('friends')->getAccount($this->raw['ToUserName']);
        } elseif (vbot('groups')->isGroup($this->raw['FromUserName'])) { // group
            $this->fromType = self::FROM_TYPE_GROUP;
        } elseif (vbot('friends')->get($this->raw['FromUserName'])) {
            $this->fromType = self::FROM_TYPE_FRIEND;
        } elseif (vbot('officials')->get($this->raw['FromUserName'])) {
            $this->fromType = self::FROM_TYPE_OFFICIAL;
        } elseif (vbot('specials')->get($this->raw['FromUserName'], false)) {
            $this->fromType = self::FROM_TYPE_SPECIAL;
        } else {
            $this->fromType = self::FROM_TYPE_UNKNOWN;
        }
    }

    private function setMessage()
    {
        $this->message = Content::formatContent($this->raw['Content']);

        if ($this->fromType === self::FROM_TYPE_GROUP) {
            $this->handleGroupContent();
        }
    }

    private function setUsername()
    {
        $this->username = $this->fromType === 'Group' ? $this->sender['UserName'] : $this->from['UserName'];
    }

    /**
     * 处理群发消息的内容.
     */
    private function handleGroupContent()
    {
        $content = $this->message;

        if (!$content || !str_contains($content, ":\n")) {
            return;
        }

        list($uid, $content) = explode(":\n", $content, 2);

        $this->sender = vbot('contacts')->getAccount($uid) ?: vbot('groups')->getMemberByUsername($this->raw['FromUserName'], $uid);
        $this->message = Content::replaceBr($content);
    }

    private function setTime()
    {
        $this->time = Carbon::createFromTimestamp($this->raw['CreateTime']);
    }

    protected function getCollection($msg, $type)
    {
        $origin = $this->create($msg);

        $this->afterCreate();

        $result = array_merge($origin, [
            'content' => $this->parseToContent(),
            'type'    => $type,
        ], $this->getExpand());

        return new Collection($result);
    }

    protected function afterCreate()
    {
    }

    protected function getExpand():array
    {
        return [];
    }

    abstract protected function parseToContent():string;

    public function __toString()
    {
        return $this->content;
    }
}
