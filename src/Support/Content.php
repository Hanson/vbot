<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/24
 * Time: 17:44.
 */

namespace Hanson\Vbot\Support;

/**
 * Content 处理类.
 *
 * Class Content
 */
class Content
{
    const EMOJI_MAP = [
        '1f63c' => '1f601',
        '1f639' => '1f602',
        '1f63a' => '1f603',
        '1f4ab' => '1f616',
        '1f64d' => '1f614',
        '1f63b' => '1f60d',
        '1f63d' => '1f618',
        '1f64e' => '1f621',
        '1f63f' => '1f622',
    ];

    /**
     * format XML for Content.
     *
     * @param $content
     *
     * @return string
     */
    public static function formatContent($content)
    {
        $content = self::emojiHandle($content);
        $content = self::replaceBr($content);

        return self::htmlDecode($content);
    }

    public static function htmlDecode($content)
    {
        return html_entity_decode($content);
    }

    public static function replaceBr($content)
    {
        return str_replace('<br/>', "\n", $content);
    }

    /**
     * 处理微信EMOJI.
     *
     * @param string $content
     *
     * @return mixed
     */
    public static function emojiHandle(string $content)
    {
        // 微信的坑
        $content = str_replace('<span class="emoji emoji1f450"></span', '<span class="emoji emoji1f450"></span>', $content);
        preg_match_all('/<span class="emoji emoji(.{1,10})"><\/span>/', $content, $match);

        foreach ($match[1] as &$unicode) {
            $unicode = array_get(self::EMOJI_MAP, $unicode, $unicode);
            $unicode = html_entity_decode("&#x{$unicode};");
        }

        return str_replace($match[0], $match[1], $content);
    }
}
