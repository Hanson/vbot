<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56
 */

namespace Hanson\Robot\Models;


use Illuminate\Support\Collection;

class OfficialAccount extends Collection
{

    /**
     * @var OfficialAccount
     */
    static $instance = null;

    /**
     * create a single instance
     *
     * @return OfficialAccount
     */
    public static function getInstance()
    {
        if(static::$instance === null){
            static::$instance = new OfficialAccount();
        }

        return static::$instance;
    }

    public function isPublic($id)
    {
        return static::$instance->get($id, false);
    }

}