<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29.
 */

namespace Hanson\Vbot\Message;

class Mina extends Message implements MessageInterface
{
    const TYPE = 'mina';

    private $title;

    private $url;

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    protected function afterCreate()
    {
        $array = (array) simplexml_load_string($this->message, 'SimpleXMLElement', LIBXML_NOCDATA);

        $info = (array) $array['appmsg'];

        $this->title = $info['title'];
        $this->url = $info['url'];
    }

    protected function getExpand():array
    {
        return ['title' => $this->title, 'url' => $this->url];
    }

    protected function parseToContent(): string
    {
        return '[小程序]';
    }
}
