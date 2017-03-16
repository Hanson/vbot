<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56
 */

namespace Hanson\Vbot\Collections;


use Illuminate\Support\Collection;

class Member extends Collection
{

    /**
     * @var Member
     */
    static $instance = null;

    /**
     * create a single instance
     *
     * @return Member
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new Member();
        }

        return static::$instance;
    }

    /**
     * 根据昵称获取群成员
     *
     * @param $groupUsername
     * @param $memberNickname
     * @param bool $blur
     * @return array'
     */
    public function getMembersByNickname($groupUsername, $memberNickname, $blur = false)
    {
        $members = $this->get($groupUsername);

        $result = [];

        foreach ($members as $username => $member) {
            if ($blur && str_contains($member['NickName'], $memberNickname)) {
                $result[] = $member;
            } elseif (!$blur && $member['NickName'] === $memberNickname) {
                $result[] = $member;
            }
        }

        return $result;
    }

}