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
    public $sender;

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

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    protected function make($msg)
    {
        $this->raw = $msg;

        $this->setFrom();
        $this->setFromType();
        $this->setMessage();

        if ($this->fromType === self::FROM_TYPE_GROUP) {
            $this->handleGroupContent($this->message);
        }

        $this->time = $msg['CreateTime'];
    }

    /**
     * 设置消息发送者.
     */
    protected function setFrom()
    {
        $this->from = $this->vb->getAccount($this->raw['FromUserName']);
    }

    protected function setFromType()
    {
        if ($this->raw['MsgType'] == 51) {
            $this->fromType = self::FROM_TYPE_SYSTEM;
        } elseif ($this->raw['FromUserName'] === myself()->username) {
            $this->fromType = self::FROM_TYPE_SELF;
            $this->from = account()->getAccount($this->raw['ToUserName']);
        } elseif (substr($this->raw['FromUserName'], 0, 2) === '@@') { // group
            $this->fromType = self::FROM_TYPE_GROUP;
        } elseif (contact()->get($this->raw['FromUserName'])) {
            $this->fromType = self::FROM_TYPE_CONTACT;
        } elseif (official()->get($this->raw['FromUserName'])) {
            $this->fromType = self::FROM_TYPE_OFFICIAL;
        } elseif (Special::getInstance()->get($this->raw['FromUserName'], false)) {
            $this->fromType = self::FROM_TYPE_SPECIAL;
        } else {
            $this->fromType = self::FROM_TYPE_UNKNOWN;
        }
    }

    protected function setMessage()
    {
        $this->message = Content::formatContent($this->raw['Content']);
    }

    /**
     * 处理群发消息的内容.
     *
     * @param $content string 内容
     */
    private function handleGroupContent($content)
    {
        if (!$content || !str_contains($content, ":\n")) {
            return;
        }
        list($uid, $content) = explode(":\n", $content, 2);

        $this->sender = account()->getAccount($uid) ?: group()->getMemberByUsername($this->raw['FromUserName'], $uid);
        $this->message = Content::replaceBr($content);
    }

    public function __toString()
    {
        return $this->content;
    }
}
