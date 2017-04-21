<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/2/12
 * Time: 20:44.
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MessageInterface;

class GroupChange extends Message implements MessageInterface
{
    public $action;

    /**
     * 群名重命名的名称.
     *
     * @var
     */
    public $rename;

    /**
     * 被踢出群时的群信息.
     *
     * @var
     */
    public $group;

    /**
     * 新人进群的昵称（可能单个可能多个）.
     *
     * @var
     */
    public $nickname;

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    public function make()
    {
        if (str_contains($this->message, '邀请你')) {
            $this->action = 'INVITE';
        } elseif (str_contains($this->message, '加入了群聊') || str_contains($this->message, '分享的二维码加入群聊')) {
            $isMatch = preg_match('/邀请"(.+)"加入了群聊/', $this->message, $match);
            if (!$isMatch) {
                preg_match('/"(.+)"通过扫描.+分享的二维码加入群聊/', $this->message, $match);
            }
            $this->action = 'ADD';
            $this->nickname = $match[1];
            group()->update($this->raw['FromUserName']);
        } elseif (str_contains($this->message, '移出了群聊')) {
            $this->action = 'REMOVE';
        } elseif (str_contains($this->message, '改群名为')) {
            $this->action = 'RENAME';
            preg_match('/改群名为“(.+)”/', $this->message, $match);
            $this->updateGroupName($match[1]);
        } elseif (str_contains($this->message, '移出群聊')) {
            $this->action = 'BE_REMOVE';
            $this->group = group()->pull($this->from['UserName']);
        }

        $this->content = $this->message;
    }

    private function updateGroupName($name)
    {
        $group = group()->get($this->from['UserName']);
        $group['NickName'] = $this->rename = $name;
        group()->put($group['UserName'], $group);
    }
}
