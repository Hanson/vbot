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

    /**
     * 增加群聊天
     *
     * @param $id
     * @param $groupMember
     */
    public function addGroupMember($id, $groupMember)
    {
        $account = static::$instance->all();

        $account[static::GROUP_MEMBER][$id] = $groupMember;

        static::$instance = static::$instance->make($account);
    }

    /**
     * 增加联系人聊天
     *
     * @param $id
     * @param $normalMember
     */
    public function addNormalMember($id, $normalMember)
    {
        $account = static::$instance->all();

        $account[static::NORMAL_MEMBER][$id] = $normalMember;

        static::$instance = static::$instance->make($account);
    }

    /**
     * 获取联系人名称
     *
     * @param string $id
     * @param string $type 群或者联系人
     * @param bool $prefer 返回最佳名称或名称数组
     * @return array|null
     */
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

        return $prefer ? current($name) : $name;
    }

    /**
     * 获取联系人
     *
     * @param $id
     * @return array
     */
    public function getContactByUsername($id)
    {
        $target = static::$instance->get(static::NORMAL_MEMBER);

        return $target[$id] ?? null;
    }

    public function getGroupMember($id)
    {
        $target = static::$instance->get(static::GROUP_MEMBER);

        return $target[$id] ?? null;
    }

    /**
     * 获取联系人列表
     *
     * @return Collection
     */
    public function getNormalMembers()
    {
        $target = static::$instance->get(static::NORMAL_MEMBER);

        return collect($target);
    }


    /**
     * 根据微信号获取联系人
     *
     * @param $id
     * @return mixed
     */
    public function getContactById($id)
    {
        $contact = $this->getNormalMembers()->filter(function($item, $key) use ($id){
            if($item['info']['Alias'] === $id){
                return true;
            }
        })->first();

        return $contact;
    }

    /**
     * 根据微信号获取联系username
     *
     * @param $id
     * @return mixed
     */
    public function getUsernameById($id)
    {
        $contact = $this->getNormalMembers()->search(function($item, $key) use ($id){
            if($item['info']['Alias'] === $id){
                return true;
            }
        });

        return $contact;
    }

}