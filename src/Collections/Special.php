<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56
 */

namespace Hanson\Vbot\Collections;


use Illuminate\Support\Collection;

class Special extends Collection
{

    static $instance = null;

    /**
     * create a single instance
     *
     * @return Special
     */
    public static function getInstance()
    {
        if(static::$instance === null){
            static::$instance = new Special();
        }

        return static::$instance;
    }

}