<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/3
 * Time: 21:54
 */

namespace Hanson\Robot\Core;


class Myself
{

    static $instance;

    public $nickname;

    public $userName;

    public $uin;

    public $sex;

    public static function getInstance()
    {
        if(!static::$instance){
            static::$instance = new Myself();
        }

        return static::$instance;
    }

    public function init($user)
    {
        $this->nickname = $user['NickName'];
        $this->userName = $user['UserName'];
        $this->sex = $user['Sex'];
        $this->uin= $user['Uin'];
    }

}