<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2017/1/13
 * Time: 15:48
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MediaTrait;
use Hanson\Vbot\Message\MessageInterface;

class Recall extends Message implements MessageInterface
{
    use MediaTrait;

    /**
     * @var Message 上一条撤回的消息
     */
    public $origin;

    /**
     * @var string 撤回者昵称
     */
    public $nickname;

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
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

    public function make()
    {
        $msgId = $this->parseMsgId($this->msg['Content']);

        /** @var Message $message */
        $this->origin = message()->get($msgId, null);

        if($this->origin){
            $this->nickname = $this->origin->sender ? $this->origin->sender['NickName'] : account()->getAccount($this->origin->msg['FromUserName'])['NickName'];
            $this->setContent();
        }

    }

    private function setContent()
    {
        $this->content = "{$this->nickname} 刚撤回了消息";
    }
}