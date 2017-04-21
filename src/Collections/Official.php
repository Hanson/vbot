<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56.
 */

namespace Hanson\Vbot\Collections;

class Official extends BaseCollection
{
    /**
     * @var Official
     */
    public static $instance = null;

    /**
     * create a single instance.
     *
     * @return Official
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function isOfficial($verifyFlag)
    {
        return ($verifyFlag & 8) != 0;
    }
}
