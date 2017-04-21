<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/2
 * Time: 22:21.
 */

namespace Hanson\Vbot\Support;

class FileManager
{
    /**
     * 下载到某个特定路径.
     *
     * @param string $file
     * @param $data
     */
    public static function saveTo(string $file, $data)
    {
        $path = dirname($file);

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        file_put_contents($file, $data);
    }

    /**
     * 下载到用户文件夹.
     *
     * @param $file
     * @param $data
     */
    public static function saveToUserPath(string $file, $data)
    {
        $file = Path::getCurrentUinPath().$file;

        static::saveTo($file, $data);
    }
}
