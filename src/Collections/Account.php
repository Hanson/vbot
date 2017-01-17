<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56
 */

namespace Hanson\Vbot\Collections;


use Illuminate\Support\Collection;

class Account
{

    /**
     * @var Group
     */
    static $group;

    /**
     * @var Contact
     */
    static $contact;

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
        if(static::$instance === null){
            static::$instance = new Account();
            static::$group = group();
            static::$contact = contact();
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
        $account = static::$group->get($username, null);

        $account = $account ? : static::$contact->get($username, null);

        return $account ? : member()->get($username, []);
    }

}