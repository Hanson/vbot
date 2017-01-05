<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56
 */

namespace Hanson\Robot\Collections;


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
        if(static::$instance === null){
            static::$instance = new Member();
        }

        return static::$instance;
    }

    /**
     * 根据username获取群成员
     *
     * @param $id
     * @return array
     */
    public function getMemberByUsername($id)
    {
        $member = $this->get($id);

        return $member ?? null;
    }

}