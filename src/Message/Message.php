<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/15
 * Time: 0:12
 */

namespace Hanson\Robot\Message;


use Hanson\Robot\Core\Server;

class Message
{

    public $sender;

    public $receiver;

    public $content;

    public $time;

    public $type;

    static $message = [];

    const USER_MAP = [
        0 => 'Init',
        1 => 'Self',
        2 => 'FileHelper',
        3 => 'Group',
        4 => 'Contact',
        5 => 'Public',
        6 => 'Special',
        99 => 'UnKnown',
    ];

    public function make($selector, $message)
    {
        $msg = $message['AddMsgList'][0];

        if($msg['MsgType'] == 51){
            $this->sender->name = 'system';
            $this->sender->type = 0;
        }elseif ($msg['MsgType'] == 37){
            $this->sender->type = 37;
        }elseif (Server::isMyself($msg['FromUserName'])){

        }
    }

}