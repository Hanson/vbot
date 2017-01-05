<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/14
 * Time: 23:08
 */

namespace Hanson\Robot\Core;

use Closure;
use Hanson\Robot\Collections\Account;
use Hanson\Robot\Message\Message;
use Hanson\Robot\Support\Console;

class MessageHandler
{
    protected $server;

    private $syncHost;

    private $handler;

    private $customHandler;

    static $instance = null;

    /**
     * get a message handler single instance
     *
     * @return MessageHandler
     */
    public static function getInstance()
    {
        if(static::$instance === null){
            static::$instance = new MessageHandler();
        }

        return static::$instance;
    }

    /**
     * 消息处理器
     *
     * @param Closure $closure
     * @throws \Exception
     */
    public function setMessageHandler(Closure $closure)
    {
        if(!$closure instanceof Closure){
            throw new \Exception('message handler must be a closure!');
        }

        $this->handler = $closure;
    }

    /**
     * 自定义处理器
     *
     * @param Closure $closure
     * @throws \Exception
     */
    public function setCustomHandler(Closure $closure)
    {
        if(!$closure instanceof Closure){
            throw new \Exception('message handler must be a closure!');
        }

        $this->customHandler = $closure;
    }

    /**
     * listen the chat api
     */
    public function listen()
    {
        $this->preCheckSync();

        while (true){

            if($this->customHandler instanceof Closure){
                call_user_func_array($this->customHandler, []);
            }

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
    }

    private function handlerMessage($selector)
    {
        if($selector === 0){
            return;
        }

        $message = $this->sync();

        if($message['AddMsgList']){
            foreach ($message['AddMsgList'] as $msg) {
                $content = (new Message)->make($selector, $msg);
                if($this->handler instanceof Closure){
                    $reply = call_user_func_array($this->handler, [$content]);
                    Message::send($reply, $content->username);
                }
            }
        }
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
                'sid' => server()->sid,
                'uin' => server()->uin,
                'skey' => server()->skey,
                'deviceid' => server()->deviceId,
                'synckey' => server()->syncKeyStr,
                '_' => time()
            ]);

        try{
            $content = http()->get($url);

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
        $url = sprintf(Server::BASE_URI . '/webwxsync?sid=%s&skey=%s&lang=en_US&pass_ticket=%s', server()->sid, server()->skey, server()->passTicket);

        try{
            $result = http()->json($url, [
                'BaseRequest' => server()->baseRequest,
                'SyncKey' => server()->syncKey,
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
        server()->syncKey = $result['SyncKey'];

        $syncKey = [];

        foreach (server()->syncKey['List'] as $item) {
            $syncKey[] = $item['Key'] . '_' . $item['Val'];
        }

        server()->syncKeyStr = implode('|', $syncKey);
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
        Console::log('[DEBUG] retcode:' . $retCode . ' selector:' . $selector);

        if($sleep){
            sleep($sleep);
        }
    }

}