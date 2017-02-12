<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/2/12
 * Time: 20:44
 */

namespace Hanson\Vbot\Message\Entity;


use Hanson\Vbot\Collections\ContactFactory;
use Hanson\Vbot\Message\MessageInterface;
use Hanson\Vbot\Support\Console;

class GroupChange extends Message implements MessageInterface
{

    public $action;

    public $rename;

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    public function make()
    {
        if(str_contains($this->msg['Content'], '加入了群聊')){
            $this->action = 'ADD';
            Console::log("检测到 {$this->from['NickName']} 有新成员，正在刷新群成员列表...");
            (new ContactFactory())->makeContactList();
            Console::log('群成员更新成功！');
        }elseif(str_contains($this->msg['Content'], '移出了群聊')){
            $this->action = 'REMOVE';
        }elseif(str_contains($this->msg['Content'], '改群名为')){
            $this->action = 'RENAME';
            preg_match('/改群名为“(.+)”/', $this->msg['Content'], $match);
            $this->updateGroupName($match[1]);
        }

        $this->content = $this->msg['Content'];
    }

    private function updateGroupName($name)
    {
        $group = group()->get($this->from['UserName']);
        $group['NickName'] = $this->rename = $name;
        group()->put($group['UserName'], $group);
    }
}