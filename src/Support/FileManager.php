<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/2
 * Time: 22:21
 */

namespace Hanson\Vbot\Support;


class FileManager
{

    public static function download($name, $data, $path = '')
    {
        $path = server()->config['tmp'] . myself()->nickname . '/' . $path;
        if(!is_dir(realpath($path))){
            mkdir($path, 0700, true);
        }

        file_put_contents("$path/$name", $data);
    }

}