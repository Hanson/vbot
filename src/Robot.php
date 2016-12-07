<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2016/12/7
 * Time: 16:12
 */

namespace Hanson\Robot;


use GuzzleHttp\Client;

class Robot
{

    private $client;

    private $uuid;

    private $baseUri;

    private $baseHost;

    private $redirectUri;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * 获取UUID
     * @throws \Exception
     */
    public function getUuid()
    {
        $content = $this->client->get('https://login.weixin.qq.com/jslogin', [
            'query' => [
                'appid' => 'wx782c26e4c19acffb',
                'fun' => 'new',
                'lang' => 'zh_CN',
                '_' => time() * 1000 . random_int(1, 999)
            ]
        ])->getBody()->getContents();

        preg_match('/window.QRLogin.code = (\d+); window.QRLogin.uuid = \"(\S+?)\"/', $content, $matches);

        if(!$matches){
            throw new \Exception('获取UUID失败');
        }

        $this->uuid = $matches[2];
    }

    public function __get($value)
    {
        return $this->$value;
    }

}