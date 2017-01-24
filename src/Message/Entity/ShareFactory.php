<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MessageInterface;
use Hanson\Vbot\Support\Content;

class ShareFactory
{

    protected $xml;

    public $type;

    public function make($msg)
    {
        $xml = Content::formatContent($msg['Content']);

        $this->parse($xml);

        if($this->type == 6){
            return new File($msg);
        }else{
            return new Share($msg);
        }
    }

    private function parse($xml)
    {
        $array = (array)simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $this->xml = $info = (array)$array['appmsg'];

        $this->type = $info['type'];
    }
}