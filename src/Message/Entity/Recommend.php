<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29.
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MessageInterface;

class Recommend extends Message implements MessageInterface
{
    /**
     * @var array 推荐信息
     */
    public $info;

    public $bigAvatar;

    public $smallAvatar;

    public $isOfficial = false;

    public $description;

    /**
     * 国内为省，国外为国.
     *
     * @var string
     */
    public $province;

    /**
     * 城市
     *
     * @var string
     */
    public $city;

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    public function make()
    {
        $this->info = $this->raw['RecommendInfo'];
        $this->parseContent();
        $this->content = '[名片推荐]';
    }

    private function parseContent()
    {
        $isMatch = preg_match('/bigheadimgurl="(http:\/\/.+?)"\ssmallheadimgurl="(http:\/\/.+?)".+province="(.+?)"\scity="(.+?)".+certflag="(\d+)"\scertinfo="(.+?)"/', $this->message, $matches);

        if ($isMatch) {
            $this->bigAvatar = $matches[1];
            $this->smallAvatar = $matches[2];
            $this->province = $matches[3];
            $this->city = $matches[4];
            $flag = $matches[5];
            $desc = $matches[6];
            if (official()->isOfficial($flag)) {
                $this->isOfficial = true;
                $this->description = $desc;
            }
        }
    }
}
