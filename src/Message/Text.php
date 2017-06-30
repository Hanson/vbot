<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/16
 * Time: 18:33.
 */

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Message\Traits\SendAble;

class Text extends Message implements MessageInterface
{
    use SendAble;

    const TYPE = 'text';
    const API = 'webwxsendmsg?';

    private $isAt = false;

    private $pure;

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    protected function afterCreate()
    {
        $this->isAt = str_contains($this->message, '@'.vbot('myself')->nickname);
        $this->pure = $this->pureText();
    }

    private function pureText()
    {
        $content = str_replace(' ', ' ', $this->message);
        $isMatch = preg_match('/^@(.+?)\s([\s\S]*)/', $content, $match);

        return $isMatch ? $match[2] : $this->message;
    }

    protected function getExpand(): array
    {
        return ['isAt' => $this->isAt, 'pure' => $this->pure];
    }

    protected function parseToContent():string
    {
        return $this->message;
    }

    /**
     * send a text message.
     *
     * @param $word string
     * @param $username string
     *
     * @return bool|mixed
     */
    public static function send($username, $word)
    {
        if (!$word || !$username) {
            return false;
        }

        return static::sendMsg([
            'Type'         => 1,
            'Content'      => $word,
            'FromUserName' => vbot('myself')->username,
            'ToUserName'   => $username,
            'LocalID'      => time() * 1e4,
            'ClientMsgId'  => time() * 1e4,
        ]);
    }
}
