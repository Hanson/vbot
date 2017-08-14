<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29.
 */

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Message\Traits\SendAble;

class Card extends Message implements MessageInterface
{
    use SendAble;

    const TYPE = 'card';
    const API = 'webwxsendmsg?';

    /**
     * @var array 推荐信息
     */
    private $info;

    private $bigAvatar;

    private $smallAvatar;

    private $isOfficial = false;

    private $description;

    /**
     * 国�
     * 为省，国外为国.
     *
     * @var string
     */
    private $province;

    /**
     * 城市
     *
     * @var string
     */
    private $city;

    public function make($msg)
    {
        return $this->getCollection($msg, static::TYPE);
    }

    protected function getExpand():array
    {
        return [
            'info'        => $this->info, 'avatar' => $this->bigAvatar, 'small_avatar' => $this->smallAvatar,
            'province'    => $this->province, 'city' => $this->city, 'description' => $this->description,
            'is_official' => $this->isOfficial,
        ];
    }

    protected function afterCreate()
    {
        $this->info = $this->raw['RecommendInfo'];
        $isMatch = preg_match('/bigheadimgurl="(http:\/\/.+?)"\ssmallheadimgurl="(http:\/\/.+?)".+province="(.+?)"\scity="(.+?)".+certflag="(\d+)"\scertinfo="(.+?)"/', $this->message, $matches);

        if ($isMatch) {
            $this->bigAvatar = $matches[1];
            $this->smallAvatar = $matches[2];
            $this->province = $matches[3];
            $this->city = $matches[4];
            $flag = $matches[5];
            $desc = $matches[6];
            if (vbot('officials')->isOfficial($flag)) {
                $this->isOfficial = true;
                $this->description = $desc;
            }
        }
    }

    protected function parseToContent(): string
    {
        return '[名片]';
    }

    public static function send($username, $alias, $nickname = null)
    {
        if (!$alias || !$username) {
            return false;
        }

        return static::sendMsg([
            'Type'         => 42,
            'Content'      => "<msg username='$alias' nickname='$nickname'/>",
            'FromUserName' => vbot('myself')->username,
            'ToUserName'   => $username,
            'LocalID'      => time() * 1e4,
            'ClientMsgId'  => time() * 1e4,
        ]);
    }
}
