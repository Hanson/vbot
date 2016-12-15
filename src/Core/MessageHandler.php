<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/14
 * Time: 23:08
 */

namespace Hanson\Robot\Core;

use Closure;
use Hanson\Robot\Message\Message;
use Hanson\Robot\Support\Log;

class MessageHandler
{
    protected $server;

    private $syncHost;

    private $handler;

    static $instance = null;

    const MESSAGE_MAP = [
        2 => 'text', // 新消息
        3 => 'unknown', // 未知
        4 => 'contactUpdate', // 通讯录更新
        6 => 'money', // 可能是红包
        7 => 'mobile' // 手机上操作了微信
    ];

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * get a message handler single instance
     *
     * @param Server $server
     * @return MessageHandler
     */
    public static function getInstance($server = null)
    {
        if(static::$instance === null){
            static::$instance = new MessageHandler($server);
        }

        return static::$instance;
    }

    public function setMessageHandler(Closure $closure)
    {
        if(!$closure instanceof Closure){
            throw new \Exception('message handler must be a closure!');
        }

        $this->handler = $closure;
    }

    /**
     * listen the chat api
     */
    public function listen()
    {
        $this->preCheckSync();

        while (true){
            $time = time();
            list($retCode, $selector) = $this->checkSync();

            if(in_array($retCode, ['1100', '1101'])){ # 微信客户端上登出或者其他设备登录
                break;
            }elseif ($retCode == 0){
                $this->handlerMessage($selector);
            }else{
                $this->debugMessage($retCode, $selector, 10);
            }

            $this->checkTime($time);
        }
//        call_user_func_array($this->handler, []);
    }

    private function handlerMessage($selector)
    {
        if($selector === 0){
            return;
        }

        $message = $this->sync();

        Message::make($selector, $message);
//        print_r($message);
        Log::echo(json_encode($message));
    }

    /**
     * get a message code
     *
     * @return array
     */
    private function checkSync()
    {
        $url = 'https://' . $this->syncHost . '/cgi-bin/mmwebwx-bin/synccheck?' . http_build_query([
                'r' => time(),
                'sid' => $this->server->sid,
                'uin' => $this->server->uin,
                'skey' => $this->server->skey,
                'deviceid' => $this->server->deviceId,
                'synckey' => $this->server->syncKeyStr,
                '_' => time()
            ]);

        try{
            $content = $this->server->http->get($url);

            preg_match('/window.synccheck=\{retcode:"(\d+)",selector:"(\d+)"\}/', $content, $matches);

            return [$matches[1], $matches[2]];
        }catch (\Exception $e){
            return [-1, -1];
        }
    }

    /**
     * test a domain before sync
     *
     * @return bool
     */
    private function preCheckSync()
    {
        foreach (['webpush.', 'webpush2.'] as $host) {
            $this->syncHost = $host . Server::BASE_HOST;
            list($retCode,) = $this->checkSync();

            if($retCode == 0){
                return true;
            }
        }

        return false;
    }

    private function sync()
    {
        $url = sprintf(Server::BASE_URI . '/webwxsync?sid=%s&skey=%s&lang=en_US&pass_ticket=%s', $this->server->sid, $this->server->skey, $this->server->passTicket);

        try{
            $result = $this->server->http->json($url, [
                    'BaseRequest' => $this->server->baseRequest,
                    'SyncKey' => $this->server->syncKey,
                    'rr' => ~time()
            ], true);

            if($result['BaseResponse']['Ret'] == 0){
                $this->generateSyncKey($result);
            }

            return $result;
        }catch (\Exception $e){
            return null;
        }
    }

    /**
     * generate a sync key
     *
     * @param $result
     */
    private function generateSyncKey($result)
    {
        $this->server->syncKey = $result['SyncKey'];

        $syncKey = [];

        foreach ($this->server->syncKey['List'] as $item) {
            $syncKey[] = $item['Key'] . '_' . $item['Val'];
        }

        $this->server->syncKeyStr = implode('|', $syncKey);
    }

    /**
     * check message time
     *
     * @param $time
     */
    private function checkTime($time)
    {
        $checkTime = time() - $time;

        if($checkTime < 0.8){
            sleep(1 - $checkTime);
        }
    }

    /**
     * debug while the sync
     *
     * @param $retCode
     * @param $selector
     * @param null $sleep
     */
    private function debugMessage($retCode, $selector, $sleep = null)
    {
        Log::echo('[DEBUG] retcode:' . $retCode . ' selector:' . $selector);

        if($sleep){
            sleep($sleep);
        }
    }

}