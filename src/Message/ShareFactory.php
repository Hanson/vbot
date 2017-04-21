<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29.
 */

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Message\Entity\File;
use Hanson\Vbot\Message\Entity\Mina;
use Hanson\Vbot\Message\Entity\Official;
use Hanson\Vbot\Message\Entity\Share;
use Hanson\Vbot\Support\Content;

class ShareFactory
{
    protected $xml;

    public $type;

    public function make($msg)
    {
        $xml = Content::formatContent($msg['Content']);

        $this->parse($xml);

        if ($this->type == 6) {
            return new File($msg);
        } elseif (official()->get($msg['FromUserName'])) {
            return new Official($msg);
        } elseif ($this->type == 33) {
            return new Mina($msg);
        } else {
            return new Share($msg);
        }
    }

    private function parse($xml)
    {
        if (starts_with($xml, '@')) {
            $xml = preg_replace('/(@\S+:\\n)/', '', $xml);
        }

        $array = (array) simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $this->xml = $info = (array) $array['appmsg'];

        $this->type = $info['type'];
    }
}
