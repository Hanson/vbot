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
        if(static::$instance === null){
            static::$instance = new Member();
        }

        return static::$instance;
    }

}