<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56.
 */

namespace Hanson\Vbot\Contact;

class Officials extends Contacts
{
    public function isOfficial($verifyFlag)
    {
        return ($verifyFlag & 8) != 0;
    }
}
