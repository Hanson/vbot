<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/2
 * Time: 22:21
 */

namespace Hanson\Robot\Support;


class FileManager
{

    public static function download($name, $data)
    {
        file_put_contents($name, $data);
    }

}