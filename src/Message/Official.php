<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29.
 */

namespace Hanson\Vbot\Message;

use Illuminate\Support\Arr;

class Official extends Message implements MessageInterface
{
    const TYPE = 'official';

    private $title;

    private $description;

    private $url;

    private $articles;

    private $app;

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    protected function afterCreate()
    {
        $array = (array) simplexml_load_string($this->message, 'SimpleXMLElement', LIBXML_NOCDATA);

        $info = (array) $array['appmsg'];

        $this->title = $info['title'];
        $this->description = (string) $info['des'];
        $this->articles = $this->getArticles($info);

        $appInfo = (array) $array['appinfo'];

        $this->app = $appInfo['appname'];

        $this->url = $this->raw['Url'];
    }

    protected function getExpand():array
    {
        return ['title' => $this->title, 'description' => $this->description, 'app' => $this->app, 'url' => $this->url,
            'articles'  => $this->articles, ];
    }

    protected function parseToContent(): string
    {
        return '[公众号消息]';
    }

    private function getArticles($info)
    {
        if ($m = (array) Arr::get($info, 'mmreader') and isset($m['category'])) {
            $articles = [];

            foreach ($m['category'] as $key => $article) {
                if ($key === 'item') {
                    $articles[] = [
                        'title' => (string) Arr::get((array) $article, 'title'),
                        'cover' => (string) Arr::get((array) $article, 'cover'),
                        'url'   => (string) Arr::get((array) $article, 'url'),
                    ];
                }
            }

            return $articles;
        }

        return [];
    }
}
