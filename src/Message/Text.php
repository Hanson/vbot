<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/16
 * Time: 18:33.
 */

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Support\Console;

class Text extends Message implements MessageInterface
{
    public $isAt;

    public $pureMessage;

    public function __construct(Vbot $vbot, $msg)
    {
        parent::__construct($vbot, $msg);

        $this->make();
    }

    /**
     * 发送消息.
     *
     * @param $word string|Text 消息内容
     * @param $username string 目标username
     *
     * @return bool
     */
    public function send($username, $word)
    {
        if (!$word || !$username) {
            return false;
        }

        $word = is_string($word) ? $word : $word->content;

        $random = strval(time() * 1000).'0'.strval(rand(100, 999));

        $data = [
            'BaseRequest' => $this->vbot->config['server.baseRequest'],
            'Msg'         => [
                'Type'         => 1,
                'Content'      => $word,
                'FromUserName' => $this->vbot->myself->username,
                'ToUserName'   => $username,
                'LocalID'      => $random,
                'ClientMsgId'  => $random,
            ],
            'Scene' => 0,
        ];
        print_r($data);return;
        $result = http()->post(server()->baseUri.'/webwxsendmsg?pass_ticket='.server()->passTicket,
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), true
        );

        if ($result['BaseResponse']['Ret'] != 0) {
            Console::log('发送消息失败 '.time(), Console::WARNING);

            return false;
        }

        return true;
    }

    public function make()
    {
        $this->isAt();
        $this->parseToContent();
    }

    private function isAt()
    {
        $this->isAt = str_contains($this->content, '@'.$this->vbot->myself->nickname);
    }

    public function parseToContent()
    {
        $this->content = $this->message;
    }
}
