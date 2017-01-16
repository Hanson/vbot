<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56
 */

namespace Hanson\Vbot\Collections;


use Hanson\Vbot\Support\Console;
use Illuminate\Support\Collection;

class Group extends Collection
{

    static $instance = null;

    /**
     * create a single instance
     *
     * @return Group
     */
    public static function getInstance()
    {
        if(static::$instance === null){
            static::$instance = new Group();
        }

        return static::$instance;
    }

    /**
     * 判断是否群组
     *
     * @param $userName
     * @return bool
     */
    public static function isGroup($userName){
        return strstr($userName, '@@') !== false;
    }

    /**
     * 根据群名筛选群组
     *
     * @param $name
     * @param bool $blur
     * @param bool $onlyUsername
     * @return static
     */
    public function getGroupsByNickname($name, $blur = false)
    {
        $groups = $this->filter(function($value, $key) use ($name, $blur){
           if(!$blur){
               return $value['NickName'] === $name;
           }else{
               return str_contains($value['NickName'], $name);
           }
        });

        return $groups;
    }

}