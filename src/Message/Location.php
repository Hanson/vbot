<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/16
 * Time: 21:13
 */

namespace Hanson\Robot\Message;


class Location
{

    public static function isLocation($content)
    {
        return str_contains('webwxgetpubliclinkimg', $content);
    }

    public static function getLocationText($content)
    {
        $result = explode('<br/>', $content);

        return current(array_slice($result, -2, 1));
    }
}