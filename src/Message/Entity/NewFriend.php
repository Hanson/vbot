<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/2/12
 * Time: 20:44.
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MessageInterface;

class NewFriend extends Message implements MessageInterface
{
    public function __construct($msg)
    {
        contact()->update($msg['FromUserName']);

        parent::__construct($msg);

        $this->make();
    }

    public function make()
    {
        $this->content = $this->message;
    }
}
