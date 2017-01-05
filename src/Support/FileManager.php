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

    public static function download($msgId, $data, $type)
    {
        $path = server()->config['tmp'] . $type;
        if(!is_dir(realpath($path))){
            mkdir($path, 0700, true);
        }

        file_put_contents("$path/$msgId.$type", $data);
    }

}