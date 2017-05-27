<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/2/12
 * Time: 20:44.
 */

namespace Hanson\Vbot\Message;

class GroupChange extends Message implements MessageInterface
{
    const TYPE = 'group_change';

    public $action;

    public $inviter;

    public $invited;

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    protected function afterCreate()
    {
        if (str_contains($this->message, '邀请你')) {
            $this->action = 'INVITE';
        } elseif (str_contains($this->message, '加入了群聊') || str_contains($this->message, '分享的二维码加入群聊')) {
            $isMatch = preg_match('/"?(.+)"?邀请"(.+)"加入了群聊/', $this->message, $match);
            if ($isMatch) {
                $this->inviter = $match[1];
                $this->invited = $match[2];
            } else {
                preg_match('/"(.+)"通过扫描"?(.+)"?分享的二维码加入群聊/', $this->message, $match);
                $this->inviter = $match[2];
                $this->invited = $match[1];
            }
            $this->action = 'ADD';
        } elseif (str_contains($this->message, '移出了群聊')) {
            $this->action = 'REMOVE';
        } elseif (str_contains($this->message, '改群名为')) {
            $this->action = 'RENAME';
        } elseif (str_contains($this->message, '移出群聊')) {
            $this->action = 'BE_REMOVE';
        }
    }

    protected function getExpand():array
    {
        return ['action' => $this->action, 'inviter' => $this->inviter, 'invited' => $this->invited];
    }

    protected function parseToContent(): string
    {
        return $this->message;
    }
}
