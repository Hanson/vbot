<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/3
 * Time: 21:54
 */

namespace Hanson\Vbot\Core;


use Hanson\Vbot\Support\Console;
use Hanson\Vbot\Support\Content;

class Myself
{

    static $instance;

    public $nickname;

    public $username;

    public $uin;

    public $sex;

    public $alias;

    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new Myself();
        }

        return static::$instance;
    }

    public function init($user)
    {
        contact()->put($user['UserName'], $user);
        $this->nickname = Content::emojiHandle($user['NickName']);
        $this->username = $user['UserName'];
        $this->sex = $user['Sex'];
        $this->uin = $user['Uin'];
        Console::log('当前用户昵称：' . $this->nickname);
        Console::log('当前用户ID：' . $this->username);
        Console::log('当前用户UIN：' . $this->uin);
    }

}