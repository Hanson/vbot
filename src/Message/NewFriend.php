<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/2/12
 * Time: 20:44.
 */

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Foundation\Vbot;

class NewFriend extends Message implements MessageInterface
{
    public function __construct(Vbot $vbot)
    {
        contact()->update($msg['FromUserName']);

        parent::__construct($vbot);

        $this->make();
    }

    public function make()
    {
        $this->content = $this->message;
    }
}
