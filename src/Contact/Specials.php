<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56.
 */

namespace Hanson\Vbot\Contact;

class Specials extends Contacts
{
    public static $instance = null;

    /**
     * create a single instance.
     *
     * @return Specials
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }
}
