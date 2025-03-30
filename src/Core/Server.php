<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/9
 * Time: 21:10.
 */

namespace Hanson\Vbot\Core;

use Carbon\Carbon;
use Hanson\Vbot\Console\Console;
use Hanson\Vbot\Exceptions\FetchUuidException;
use Hanson\Vbot\Exceptions\InitFailException;
use Hanson\Vbot\Exceptions\LoginFailedException;
use Hanson\Vbot\Exceptions\LoginTimeoutException;
use Hanson\Vbot\Foundation\Vbot;

class Server
{
    /**
     * @var Vbot
     */
    protected $vbot;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    public function serve()
    {
        if (!$this->tryLogin()) {
            $this->cleanCookies();
            $this->login();
        }

        $this->init();

        if ($this->vbot->config['swoole.status']) {
            $this->vbot->swoole->run();
        } else {
            $this->vbot->messageHandler->listen();
        }
    }

    /**
     * 尝试登录.
     *
     * @return bool
     */
    private function tryLogin(): bool
    {
        if (is_file($this->vbot->config['cookie_file']) && $this->vbot->cache->has($this->vbot->config['session_key'])) {
            $configs = json_decode($this->vbot->cache->get($this->vbot->config['session_key']), true);

            $this->vbot->config['server'] = $configs;
            $this->vbot->config['server.time'] = $this->vbot->config['server.time'] ?: Carbon::now()->toDateTimeString();

            if (!($checkSync = $this->vbot->sync->checkSync())) {
                return false;
            }

            $result = $this->vbot->messageHandler->handleCheckSync($checkSync[0], $checkSync[1], true);

            if ($result) {
                $this->vbot->reLoginSuccessObserver->trigger();

                return true;
            }
        }
        $this->vbot->config['server.time'] = Carbon::now()->toDateTimeString();

        return false;
    }

    private function cleanCookies()
    {
        $this->vbot->console->log('cleaning useless cookies.');
        if (is_file($this->vbot->config['cookie_file'])) {
            unlink($this->vbot->config['cookie_file']);
        }
    }

    /**
     * login.
     */
    public function login()
    {
        $this->getUuid();
        $this->showQrCode();
        $this->waitForLogin();
        $this->getLogin();
    }

    /**
     * get uuid.
     *
     * @throws \Exception
     */
    protected function getUuid()
    {
        $content = $this->vbot->http->get('https://login.weixin.qq.com/jslogin', ['query' => [
            'appid'       => 'wx782c26e4c19acffb',
            'fun'         => 'new',
            'lang'        => 'zh_CN',
            'redirect_uri'=> 'https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxnewloginpage?mod=desktop',
            '_'           => time(),
        ]]);

        preg_match('/window.QRLogin.code = (\d+); window.QRLogin.uuid = \"(\S+?)\"/', $content, $matches);

        if (!$matches) {
            throw new FetchUuidException('fetch uuid failed.');
        }

        $this->vbot->config['server.uuid'] = $matches[2];
    }

    /**
     * show a login qrCode.
     */
    public function showQrCode()
    {
        $url = 'https://login.weixin.qq.com/l/'.$this->vbot->config['server.uuid'];

        $this->vbot->qrCodeObserver->trigger($url);

        $this->vbot->qrCode->show($url);
    }

    /**
     * waiting user to login.
     *
     * @throws \Exception
     */
    protected function waitForLogin()
    {
        $retryTime = 10;
        $tip = 1;

        $this->vbot->console->log('please scan the qrCode with wechat.');
        while ($retryTime > 0) {
            $url = sprintf('https://login.weixin.qq.com/cgi-bin/mmwebwx-bin/login?tip=%s&uuid=%s&_=%s', $tip, $this->vbot->config['server.uuid'], time());

            $content = $this->vbot->http->get($url, ['timeout' => 35]);

            preg_match('/window.code=(\d+);/', $content, $matches);

            $code = $matches[1];
            switch ($code) {
                case '201':
                    $this->vbot->console->log('please confirm login in wechat.');
                    $tip = 0;
                    break;
                case '200':
                    preg_match('/window.redirect_uri="(https:\/\/(\S+?)\/\S+?)";/', $content, $matches);

                    $this->vbot->config['server.uri.redirect'] = $matches[1].'&fun=new&version=v2';
                    $url = 'https://%s/cgi-bin/mmwebwx-bin';
                    $this->vbot->config['server.uri.file'] = sprintf($url, 'file.'.$matches[2]);
                    $this->vbot->config['server.uri.push'] = sprintf($url, 'webpush.'.$matches[2]);
                    $this->vbot->config['server.uri.base'] = sprintf($url, $matches[2]);

                    return;
                default:
                    $tip = 1;
                    $retryTime -= 1;
                    sleep(1);
                    break;
            }
        }

        $this->vbot->console->log('login time out!', Console::ERROR);

        throw new LoginTimeoutException('Login time out.');
    }

    /**
     * login wechat.
     *
     * @throws \Exception
     */
    private function getLogin()
    {
        $options = [
            'headers' => [
                'referer'        => 'https://wx.qq.com/?&lang=zh_CN&target=t',
                'client-version' => '2.0.0',
                'user-agent'     => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36',
                'extspam'        => 'Go8FCIkFEokFCggwMDAwMDAwMRAGGvAESySibk50w5Wb3uTl2c2h64jVVrV7gNs06GFlWplHQbY/5FfiO++1yH4ykCyNPWKXmco+wfQzK5R98D3so7rJ5LmGFvBLjGceleySrc3SOf2Pc1gVehzJgODeS0lDL3/I/0S2SSE98YgKleq6Uqx6ndTy9yaL9qFxJL7eiA/R3SEfTaW1SBoSITIu+EEkXff+Pv8NHOk7N57rcGk1w0ZzRrQDkXTOXFN2iHYIzAAZPIOY45Lsh+A4slpgnDiaOvRtlQYCt97nmPLuTipOJ8Qc5pM7ZsOsAPPrCQL7nK0I7aPrFDF0q4ziUUKettzW8MrAaiVfmbD1/VkmLNVqqZVvBCtRblXb5FHmtS8FxnqCzYP4WFvz3T0TcrOqwLX1M/DQvcHaGGw0B0y4bZMs7lVScGBFxMj3vbFi2SRKbKhaitxHfYHAOAa0X7/MSS0RNAjdwoyGHeOepXOKY+h3iHeqCvgOH6LOifdHf/1aaZNwSkGotYnYScW8Yx63LnSwba7+hESrtPa/huRmB9KWvMCKbDThL/nne14hnL277EDCSocPu3rOSYjuB9gKSOdVmWsj9Dxb/iZIe+S6AiG29Esm+/eUacSba0k8wn5HhHg9d4tIcixrxveflc8vi2/wNQGVFNsGO6tB5WF0xf/plngOvQ1/ivGV/C1Qpdhzznh0ExAVJ6dwzNg7qIEBaw+BzTJTUuRcPk92Sn6QDn2Pu3mpONaEumacjW4w6ipPnPw+g2TfywJjeEcpSZaP4Q3YV5HG8D6UjWA4GSkBKculWpdCMadx0usMomsSS/74QgpYqcPkmamB4nVv1JxczYITIqItIKjD35IGKAUwAA==',
            ],
        ];
        $content = $this->vbot->http->get($this->vbot->config['server.uri.redirect'], $options);
        $data = (array) simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);

        if (isset($data['ret']) && $data['ret'] == 1203) {
            throw new LoginFailedException($data['message']);
        }
        $this->vbot->config['server.skey'] = $data['skey'];
        $this->vbot->config['server.sid'] = $data['wxsid'];
        $this->vbot->config['server.uin'] = $data['wxuin'];
        $this->vbot->config['server.passTicket'] = $data['pass_ticket'];

        if (in_array('', [$data['wxsid'], $data['wxuin'], $data['pass_ticket']])) {
            throw new LoginFailedException('Login failed.');
        }

        $this->vbot->config['server.deviceId'] = 'e'.substr(mt_rand().mt_rand(), 1, 15);

        $this->vbot->config['server.baseRequest'] = [
            'Uin'      => $data['wxuin'],
            'Sid'      => $data['wxsid'],
            'Skey'     => $data['skey'],
            'DeviceID' => $this->vbot->config['server.deviceId'],
        ];

        $this->saveServer();
    }

    /**
     * store config to cache.
     */
    private function saveServer()
    {
        $this->vbot->cache->forever('session.'.$this->vbot->config['session'], json_encode($this->vbot->config['server']));
    }

    /**
     * init.
     *
     * @param bool $first
     *
     * @throws InitFailException
     */
    protected function init($first = true)
    {
        $this->beforeInitSuccess();
        $url = $this->vbot->config['server.uri.base'].'/webwxinit?r='.(-time() / 1579).'&pass_ticket='.$this->vbot->config['server.passTicket'];

        $result = $this->vbot->http->post($url, json_encode([
            'BaseRequest' => $this->vbot->config['server.baseRequest'],
        ], JSON_UNESCAPED_SLASHES), true);

        ApiExceptionHandler::handle($result, function ($result) {
            $this->vbot->cache->forget('session.'.$this->vbot->config['session']);
            $this->vbot->log->error('Init failed.'.json_encode($result));

            throw new InitFailException('Init failed.');
        });

        $this->generateSyncKey($result, $first);

        $this->vbot->myself->init($result['User']);

        $this->afterInitSuccess($result);

        $this->initContactList($result['ContactList']);
        $this->initContact();
    }

    /**
     * before init success.
     */
    private function beforeInitSuccess()
    {
        $this->vbot->console->log('current session: '.$this->vbot->config['session']);
        $this->vbot->console->log('init begin.');
    }

    /**
     * after init success.
     *
     * @param $content
     */
    private function afterInitSuccess($content)
    {
        $this->vbot->log->info('response:'.json_encode($content));
        $this->vbot->console->log('init success.');
        $this->vbot->loginSuccessObserver->trigger();
        $this->vbot->console->log('init contacts begin.');
    }

    protected function initContactList($contactList)
    {
        if ($contactList) {
            $this->vbot->contactFactory->store($contactList);
        }
    }

    protected function initContact()
    {
        $this->vbot->contactFactory->fetchAll();
    }

    /**
     * open wechat status notify.
     */
    protected function statusNotify()
    {
        $url = sprintf($this->vbot->config['server.uri.base'].'/webwxstatusnotify?lang=zh_CN&pass_ticket=%s', $this->vbot->config['server.passTicket']);

        $this->vbot->http->json($url, [
            'BaseRequest'  => $this->vbot->config['server.baseRequest'],
            'Code'         => 3,
            'FromUserName' => $this->vbot->myself->username,
            'ToUserName'   => $this->vbot->myself->username,
            'ClientMsgId'  => time(),
        ]);
    }

    protected function generateSyncKey($result, $first)
    {
        $this->vbot->config['server.syncKey'] = $result['SyncKey'];

        $syncKey = [];

        if (is_array($this->vbot->config['server.syncKey.List'])) {
            foreach ($this->vbot->config['server.syncKey.List'] as $item) {
                $syncKey[] = $item['Key'].'_'.$item['Val'];
            }
        } elseif ($first) {
            $this->init(false);
        }

        $this->vbot->config['server.syncKeyStr'] = implode('|', $syncKey);
    }
    /**
     * logout wechat
     */
    protected function logout(){
        $url = 'https://wx.qq.com/cgi-bin/mmwebwx-bin/webwxlogout?redirect=1&type=0&skey='.$this->vbot->config['server.skey'];
        $this->vbot->http->post($url, ['sid'=>$this->vbot->config['server.sid'], 'uin'=>$this->vbot->config['server.uin']]);
    }
}
