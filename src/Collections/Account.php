<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56.
 */

namespace Hanson\Vbot\Collections;

class Account
{
    /**
     * @var Account
     */
    public static $instance = null;

    /**
     * create a single instance.
     *
     * @return Account
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * 根据username获取账号.
     *
     * @param $username
     *
     * @return mixed
     */
    public function getAccount($username)
    {
        if (starts_with($username, '@@')) {
            return group()->get($username);
        } else {
            $account = contact()->get($username, null);

            $account = $account ?: member()->get($username, null);

            $account = $account ?: official()->get($username, null);

            return $account ?: Special::getInstance()->get($username, null);
        }
    }
}
