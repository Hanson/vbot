<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56
 */

namespace Hanson\Robot\Collections;


use Hanson\Robot\Support\Console;
use Illuminate\Support\Collection;

class Message extends Collection
{

    static $instance = null;

    /**
     * create a single instance
     *
     * @return Message
     */
    public static function getInstance()
    {
        if(static::$instance === null){
            static::$instance = new Message();
        }

        return static::$instance;
    }

}