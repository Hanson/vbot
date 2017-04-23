<?php

namespace Hanson\Vbot\Contact;

use Hanson\Vbot\Support\Content;

class Myself
{
    public $nickname;

    public $username;

    public $uin;

    public $sex;

    public $alias;

    public function init($user)
    {
        //        contact()->put($user['UserName'], $user);
        $this->nickname = Content::emojiHandle($user['NickName']);
        $this->username = $user['UserName'];
        $this->sex = $user['Sex'];
        $this->uin = $user['Uin'];
//        Console::log('当前用户昵称：' . $this->nickname);
//        Console::log('当前用户ID：' . $this->username);
//        Console::log('当前用户UIN：' . $this->uin);
    }
}
