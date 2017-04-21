<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29.
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MessageInterface;

/**
 * Class Transfer.
 */
class Transfer extends Message implements MessageInterface
{
    /**
     * 转账金额 单位 元.
     *
     * @var string
     */
    public $fee;

    /**
     * 交易流水号.
     *
     * @var
     */
    public $transactionId;

    /**
     * 转账说明.
     *
     * @var string
     */
    public $memo;

    /**
     * Transfer constructor.
     *
     * @param $msg
     */
    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    public function make()
    {
        $array = (array) simplexml_load_string($this->message, 'SimpleXMLElement', LIBXML_NOCDATA);

        $des = (array) $array['appmsg']->des;
        $fee = (array) $array['appmsg']->wcpayinfo;

        $this->content = current($des);

        $this->memo = is_string($fee['pay_memo']) ? $fee['pay_memo'] : null;
        $this->fee = substr($fee['feedesc'], 3);
        $this->transactionId = $fee['transcationid'];
    }
}
