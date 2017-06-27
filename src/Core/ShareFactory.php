<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29.
 */

namespace Hanson\Vbot\Core;

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\File;
use Hanson\Vbot\Message\Mina;
use Hanson\Vbot\Message\Official;
use Hanson\Vbot\Message\Share;
use Hanson\Vbot\Support\Content;

class ShareFactory
{
    public $type;
    /**
     * @var Vbot
     */
    private $vbot;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    public function make($msg)
    {
        try {
            $xml = Content::formatContent($msg['Content']);

            $this->parse($xml);

            if ($this->type == 6) {
                return (new File())->make($msg);
            } elseif ($this->vbot->officials->get($msg['FromUserName'])) {
                return (new Official())->make($msg);
            } elseif ($this->type == 33) {
                return (new Mina())->make($msg);
            } else {
                return (new Share())->make($msg);
            }
        } catch (\Exception $e) {
            return;
        }
    }

    private function parse($xml)
    {
        if (starts_with($xml, '@')) {
            $xml = preg_replace('/(@\S+:\\n)/', '', $xml);
        }

        $array = (array) simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);

        $info = (array) $array['appmsg'];

        $this->type = $info['type'];
    }
}
