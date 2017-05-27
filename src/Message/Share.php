<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29.
 */

namespace Hanson\Vbot\Message;

class Share extends Message implements MessageInterface
{
    const TYPE = 'share';

    private $title;

    private $description;

    private $url;

    private $app;

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    protected function afterCreate()
    {
        $array = (array) simplexml_load_string($this->message, 'SimpleXMLElement', LIBXML_NOCDATA);

        $info = (array) $array['appmsg'];

        $this->title = strval($info['title']);
        $this->description = strval($info['des']);

        $appInfo = (array) $array['appinfo'];
        $this->app = strval($appInfo['appname']);

        $this->url = $this->raw['Url'];
    }

    protected function getExpand():array
    {
        return ['title' => $this->title, 'description' => $this->description, 'app' => $this->app, 'url' => $this->url];
    }

    protected function parseToContent(): string
    {
        return '[分享]';
    }
}
