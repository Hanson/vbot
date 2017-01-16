<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MessageInterface;

class Transfer extends Message implements MessageInterface
{

    /**
     * 转账金额 单位 元
     *
     * @var string
     */
    public $fee;

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    public function make()
    {
        $array = (array)simplexml_load_string($this->msg['Content'], 'SimpleXMLElement', LIBXML_NOCDATA);

        $des = (array)$array['appmsg']->des;
        $fee = (array)$array['appmsg']->wcpayinfo;

        $this->content = current($des);

        $this->fee = substr($fee['feedesc'], 3);
    }
}