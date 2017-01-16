<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56
 */

namespace Hanson\Vbot\Collections;


use Illuminate\Support\Collection;

class SpecialAccount extends Collection
{

    static $instance = null;

    /**
     * create a single instance
     *
     * @return SpecialAccount
     */
    public static function getInstance()
    {
        if(static::$instance === null){
            static::$instance = new SpecialAccount();
        }

        return static::$instance;
    }

}