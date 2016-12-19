<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56
 */

namespace Hanson\Robot\Collections;


use Illuminate\Support\Collection;

class GroupAccount extends Collection
{

    static $instance = null;

    /**
     * create a single instance
     *
     * @return GroupAccount
     */
    public static function getInstance()
    {
        if(static::$instance === null){
            static::$instance = new GroupAccount();
        }

        return static::$instance;
    }

}