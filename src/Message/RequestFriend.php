<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29.
 */

namespace Hanson\Vbot\Message;

class RequestFriend extends Message implements MessageInterface
{
    const TYPE = 'request_friend';

    /**
     * @var array 信息
     */
    private $info;

    private $avatar;

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    protected function afterCreate()
    {
        $this->info = $this->raw['RecommendInfo'];
        $isMatch = preg_match('/bigheadimgurl="(.+?)"/', $this->message, $matches);

        if ($isMatch) {
            $this->avatar = $matches[1];
        }
    }

    protected function getExpand():array
    {
        return ['info' => $this->info, 'avatar' => $this->avatar];
    }

    protected function parseToContent(): string
    {
        return '[请求添加好友]';
    }
}
