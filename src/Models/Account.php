<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56
 */

namespace Hanson\Robot\Models;


use Illuminate\Support\Collection;

class Account extends Collection
{

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
        }

        return static::$instance;
    }

}