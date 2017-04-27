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

class Message
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
     * @var string 经过处理的内容 （与类型相关 友好显示的文字）
     */
    public $content;

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

    /**
     * @var Vbot
     */
    protected $vbot;

    public function __construct(Vbot $vbot, $msg)
    {
        $this->vbot = $vbot;
        $this->raw = $msg;

        $this->create();
    }

    private function create()
    {
        $this->setFrom();
        $this->setFromType();
        $this->setMessage();
        $this->setTime();
    }

    /**
     * 设置消息发送者.
     */
    private function setFrom()
    {
        $this->from = $this->vbot->contacts->getAccount($this->raw['FromUserName']);
    }

    private function setFromType()
    {
        if ($this->raw['MsgType'] == 51) {
            $this->fromType = self::FROM_TYPE_SYSTEM;
        } elseif ($this->raw['FromUserName'] === $this->vbot->myself->username) {
            $this->fromType = self::FROM_TYPE_SELF;
            $this->from = $this->vbot->friends->getAccount($this->raw['ToUserName']);
        } elseif ($this->vbot->groups->isGroup($this->raw['FromUserName'])) { // group
            $this->fromType = self::FROM_TYPE_GROUP;
        } elseif ($this->vbot->friends->get($this->raw['FromUserName'])) {
            $this->fromType = self::FROM_TYPE_FRIEND;
        } elseif ($this->vbot->officials->get($this->raw['FromUserName'])) {
            $this->fromType = self::FROM_TYPE_OFFICIAL;
        } elseif ($this->vbot->specials->get($this->raw['FromUserName'], false)) {
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

        $this->sender = $this->vbot->contacts->getAccount($uid) ?: $this->vbot->groups->getMemberByUsername($this->raw['FromUserName'], $uid);
        $this->message = Content::replaceBr($content);
    }

    private function setTime()
    {
        $this->time = Carbon::createFromTimestamp($this->raw['CreateTime']);
    }

    public function __toString()
    {
        return $this->content;
    }
}
