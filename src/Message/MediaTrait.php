<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 3:20
 */

namespace Hanson\Vbot\Message;
use Hanson\Vbot\Support\System;


/**
 * Class MediaTrait
 * @property string $folder
 * @package Hanson\Vbot\Message
 */
trait MediaTrait
{

    public static function getPath($folder)
    {
        $path = System::getPath() . $folder;

        return realpath($path);
    }

}