<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/15
 * Time: 0:12
 */

namespace Hanson\Vbot\Message\Entity;


use Carbon\Carbon;
use Hanson\Vbot\Core\Server;
use Hanson\Vbot\Collections\Contact;
use Hanson\Vbot\Collections\Official;
use Hanson\Vbot\Collections\Special;
use Hanson\Vbot\Support\Content;
use Hanson\Vbot\Support\FileManager;
use Hanson\Vbot\Support\Console;

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

    public $msg;

    static $mediaCount = -1;

    public function __construct($msg)
    {
        $this->msg = $msg;

        $this->setFrom();
        $this->setFromType();

        $this->msg['Content'] = Content::formatContent($this->msg['Content']);
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

    private function setFromType()
    {
        if ($this->msg['MsgType'] == 51) {
            $this->fromType = 'System';
        } elseif ($this->msg['FromUserName'] === myself()->username) {
            $this->fromType = 'Self';
            $this->from = account()->getAccount($this->msg['ToUserName']);
        } elseif (substr($this->msg['FromUserName'], 0, 2) === '@@') { # group
            $this->fromType = 'Group';
        } elseif (contact()->get($this->msg['FromUserName'])) {
            $this->fromType = 'Contact';
        } elseif (official()->get($this->msg['FromUserName'])) {
            $this->fromType = 'Official';
        } elseif (Special::getInstance()->get($this->msg['FromUserName'], false)) {
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
        $this->msg['Content'] = Content::replaceBr($content);
    }

    public function __toString()
    {
        return $this->content;
    }

}