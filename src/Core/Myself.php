<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/3
 * Time: 21:54
 */

namespace Hanson\Vbot\Core;


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
        $this->nickname = $user['NickName'];
        $this->username = $user['UserName'];
        $this->sex = $user['Sex'];
        $this->uin = $user['Uin'];
    }

}