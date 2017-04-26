<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29.
 */

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Message\MessageInterface;
use Hanson\Vbot\Foundation\Vbot;

class Mina extends Message implements MessageInterface
{
    public $title;

    public $url;

    public function __construct(Vbot $vbot)
    {
        parent::__construct($vbot);

        $this->make();
    }

    public function make()
    {
        $array = (array) simplexml_load_string($this->message, 'SimpleXMLElement', LIBXML_NOCDATA);

        $info = (array) $array['appmsg'];

        $this->title = $info['title'];
        $this->url = $info['url'];

        $this->content = '[小程序]';
    }
}
