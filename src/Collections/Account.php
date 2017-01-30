<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56
 */

namespace Hanson\Vbot\Collections;


class Account
{

    /**
     * @var Account
     */
    static $instance = null;

    /**
     * create a single instance
     *
     * @return Account
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new Account();
        }

        return static::$instance;
    }

    /**
     * 根据username获取账号
     *
     * @param $username
     * @return mixed
     */
    public function getAccount($username)
    {
        $account = group()->get($username, null);

        $account = $account ?: contact()->get($username, null);

        $account = $account ?: official()->get($username, null);

        return $account ?: member()->get($username, []);
    }

}