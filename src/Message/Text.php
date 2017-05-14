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

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    /**
     * the message is at robot.
     *
     * @return bool
     */
    private function isAt()
    {
        return  str_contains($this->content, '@'.\vbot('myself')->nickname);
    }

    protected function getExpand(): array
    {
        return ['isAt' => $this->isAt()];
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
        vbot('console')->log('send :' . $word . ' to:' . $username);
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
