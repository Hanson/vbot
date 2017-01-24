<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MessageInterface;

class Share extends Message implements MessageInterface
{

    public $title;

    public $description;

    public $url;

    public $app;

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    public function make()
    {
        $array = (array)simplexml_load_string($this->msg['Content'], 'SimpleXMLElement', LIBXML_NOCDATA);

        $info = (array)$array['appmsg'];

        $this->title = $info['title'];
        $this->description = $info['des'];

        $appInfo = (array)$array['appinfo'];

        $this->app = $appInfo['appname'];

        $this->url = $this->msg['Url'];
    }
}