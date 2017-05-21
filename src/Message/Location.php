<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/16
 * Time: 21:13.
 */

namespace Hanson\Vbot\Message;

class Location extends Message implements MessageInterface
{
    const TYPE = 'location';

    /**
     * 判断是否位置消息.
     *
     * @param $content
     *
     * @return bool
     */
    public static function isLocation($content)
    {
        return str_contains($content['Content'], 'webwxgetpubliclinkimg') && $content['Url'];
    }

    private function locationUrl()
    {
        return $this->raw['Url'];
    }

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    protected function parseToContent():string
    {
        if ($this->raw['FileName'] === '我发起了位置共享') {
            return '[共享位置]';
        } else {
            return current(explode(":\n", $this->message));
        }
    }

    protected function getExpand(): array
    {
        return ['url' => $this->locationUrl()];
    }
}
