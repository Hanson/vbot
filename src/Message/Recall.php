<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2017/1/13
 * Time: 15:48
 */

namespace Hanson\Robot\Message;


class Recall extends Message 
{
    public $raw;

    /**
     * @var string 上一条撤回的msgId
     */
    public $msgId;

    public $msg;

    public $content;

    public function __construct(array $msg)
    {
        $this->raw = $msg;


        $msgId = $this->parseMsgId($this->rawMsg['Content']);
        $this->msg = $message = message()->get($msgId);
        $nickname = $message['sender'] ? $message['sender']['NickName'] : account()->getAccount($message['username'])['NickName'];
        $this->content = "{$nickname} 刚撤回了消息 " . $message['type'] === 'Text' ? "\"{$message['content']}\"" : null;
    }

    /**
     * 解析message获取msgId
     *
     * @param $xml
     * @return string msgId
     */
    private function parseMsgId($xml)
    {
        preg_match('/<msgid>(\d+)<\/msgid>/', $xml, $matches);
        return $matches[1];
    }
}