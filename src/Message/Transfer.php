<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29.
 */

namespace Hanson\Vbot\Message;

/**
 * Class Transfer.
 */
class Transfer extends Message implements MessageInterface
{
    const TYPE = 'transfer';

    /**
     * 转账金额 单位 元.
     *
     * @var string
     */
    private $fee;

    /**
     * 交易流水号.
     *
     * @var
     */
    private $transactionId;

    /**
     * 转账说明.
     *
     * @var string
     */
    private $memo;

    private $content;

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    protected function afterCreate()
    {
        $array = (array) simplexml_load_string($this->message, 'SimpleXMLElement', LIBXML_NOCDATA);

        $des = (array) $array['appmsg']->des;
        $fee = (array) $array['appmsg']->wcpayinfo;

        $this->content = current($des);

        $this->memo = is_string($fee['pay_memo']) ? $fee['pay_memo'] : null;
        $this->fee = substr($fee['feedesc'], 3);
        $this->transactionId = $fee['transcationid'];
    }

    protected function getExpand():array
    {
        return ['fee' => $this->fee, 'transaction_id' => $this->transactionId, 'memo' => $this->memo];
    }

    protected function parseToContent(): string
    {
        return $this->content;
    }
}
