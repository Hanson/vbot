<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2016/12/7
 * Time: 16:12
 */

namespace Hanson\Robot;


use Endroid\QrCode\QrCode;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class Robot
{

    private $client;

    public $tmpPath;

    private $uuid;

    private $baseUri = 'https://wx2.qq.com/cgi-bin/mmwebwx-bin';

    private $baseHost = 'wx2.qq.com';

    private $redirectUri;

    private $skey;

    private $sid;

    private $uin;

    private $passTicket;

    private $deviceId;

    private $baseRequest;

    public function __construct(Array $option = [])
    {
        $this->client = new Client();

        $this->handleOption($option);
    }

    /**
     * 处理option
     *
     * @param $option
     */
    private function handleOption($option)
    {
        $this->tmpPath = $option['tmp'] ?? sys_get_temp_dir();
    }

    public function run()
    {
        $this->getUuid();

        $this->generateQrCode();

        $this->log('[INFO] 请用微信扫码');

        if($this->waitForLogin() !== 200){
            die('[ERROR] 微信登录失败');
        }

        if(!$this->login()){
            die('[ERROR] 登录失败');
        }

        $this->log('[INFO] 登录成功');

        $this->init();
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

    /**
     * 生成登录二维码
     */
    public function generateQrCode()
    {
        $url = 'https://login.weixin.qq.com/l/' . $this->uuid;

        $qrCode = new QrCode($url);

        $file = $this->tmpPath . 'login_qr_code.png';

        $qrCode->save($file);

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            system($file);
        }
    }

    /**
     * 等待登录
     *
     * @return int
     */
    public function waitForLogin(): int
    {
        $urlTemplate = 'https://login.weixin.qq.com/cgi-bin/mmwebwx-bin/login?tip=%s&uuid=%s&_=%s';

        $retryTime = 10;
        $tip = 1;
        $code = 0;

        while($retryTime > 0){
            $url = sprintf($urlTemplate, $tip, $this->uuid, time());

            $content = $this->client->get($url)->getBody()->getContents();

            preg_match('/window.code=(\d+);/', $content, $matches);

            $code = $matches[1];
            switch($code){
                case '201':
                    $this->log('[INFO] 请点击确认进行登录');
                    $tip = 0;
                    break;
                case '200':
                    preg_match('/window.redirect_uri="(\S+?)";/', $content, $matches);
                    $this->redirectUri = $matches[1] . '&fun=new';
                    return $code;
                case '408':
                    $this->log('[ERROR] 微信登录超时。将在 1 秒后重试');
                    $tip = 1;
                    $retryTime -= 1;
                    sleep(1);
                    break;
                default:
                    $this->log('[ERROR] 微信登录异常。异常码：%s 。1 秒后重试', $code);
                    $tip = 1;
                    $retryTime -= 1;
                    sleep(1);
                    break;
            }
        }
        return $code;
    }

    public function login()
    {
        $content = $this->client->get($this->redirectUri)->getBody()->getContents();

        $crawler = new Crawler($content);
        $this->skey = $crawler->filter('error skey')->text();
        $this->sid = $crawler->filter('error wxsid')->text();
        $this->uin = $crawler->filter('error wxuin')->text();
        $this->passTicket = $crawler->filter('error pass_ticket')->text();

        if(in_array('', [$this->skey, $this->sid, $this->uin, $this->passTicket])){
            return false;
        }

        $this->deviceId = 'e' . strval(random_int(100000000000000, 999999999999999));

        $this->baseRequest = [
            'Uin' => $this->uin,
            'Sid' => $this->sid,
            'Skey' => $this->skey,
            'DeviceID' => $this->deviceId
        ];

        return true;
    }

    public function init()
    {
        $url = sprintf($this->baseUri . '/webwxinit?r=%i&lang=en_US&pass_ticket=%s', time(), $this->passTicket);

        $content = $this->client->post($url, [
            'query' => [
                'BaseRequest' => json_encode($this->baseRequest)
            ]
        ])->getBody()->getContents();

        print_r($content);
    }

    private function log($msg, ...$args)
    {
        echo sprintf($msg . PHP_EOL, $args);
    }

    public function __get($value)
    {
        return $this->$value;
    }

}