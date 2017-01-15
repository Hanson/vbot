<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 3:20
 */

namespace Hanson\Robot\Message;


/**
 * Class MediaTrait
 * @property string $folder
 * @package Hanson\Robot\Message
 */
trait MediaTrait
{

    public function getPath($folder)
    {
        $path = server()->config['tmp'] . $folder;

        return realpath($path);
    }

}