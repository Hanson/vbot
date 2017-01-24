<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/24
 * Time: 17:44
 */

namespace Hanson\Vbot\Support;


/**
 * Content 处理类
 *
 * Class Content
 * @package Hanson\Vbot\Support
 */
class Content
{

    /**
     * 格式化Content
     *
     * @param $content
     * @return string
     */
    public static function formatContent($content)
    {
        return self::htmlDecode(self::replaceBr($content));
    }

    public static function htmlDecode($content)
    {
        return html_entity_decode($content);
    }

    public static function replaceBr($content)
    {
        return str_replace('<br/>', "\n", $content);
    }
}