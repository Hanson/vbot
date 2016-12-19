<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56
 */

namespace Hanson\Robot\Collections;


use Illuminate\Support\Collection;

class Account extends Collection
{

    /**
     * @var Account
     */
    static $instance = null;

    const NORMAL_MEMBER = 'normal_member';

    const GROUP_MEMBER = 'group_member';

    /**
     * create a single instance
     *
     * @return Account
     */
    public static function getInstance()
    {
        if(static::$instance === null){
            static::$instance = new Account();
        }

        return static::$instance;
    }

    public function addGroupMember($groupMember)
    {
        $account = static::$instance->all();

        $account[static::GROUP_MEMBER][] = $groupMember;

        static::$instance->make($account);
    }

    public function addNormalMember($normalMember)
    {
        $account = static::$instance->all();

        $account[static::NORMAL_MEMBER][] = $normalMember;

        static::$instance->make($account);
    }

    public function getContactName($id, $type, $prefer = false)
    {
        $target = static::$instance->get($type);
        $user = $target[$id];
        $name = [];

        if(isset($user['RemarkName'])){
            $name['remarkName'] = $user['RemarkName'];
        }
        if(isset($user['NickName'])){
            $name['nickName'] = $user['NickName'];
        }
        if(isset($user['DisplayName'])){
            $name['displayName'] = $user['DisplayName'];
        }

        if(!$name){
            return null;
        }

        return $prefer ? array_values($name)[0] : $name;
    }

}