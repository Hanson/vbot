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

class NewFriend extends Message implements MessageInterface
{

    public function __construct($msg)
    {
        $this->make();
        parent::__construct($msg);
    }

    public function make()
    {
        Console::log('检测到新加好友，正在刷新好友列表...');
        (new ContactFactory())->makeContactList();
        Console::log('好友更新成功！');
    }
}