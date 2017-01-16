<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/14
 * Time: 23:08
 */

namespace Hanson\Vbot\Core;

use Closure;
use Hanson\Vbot\Collections\Account;
use Hanson\Vbot\Message\Entity\Emoticon;
use Hanson\Vbot\Message\Entity\Image;
use Hanson\Vbot\Message\Entity\Message;
use Hanson\Vbot\Message\Entity\Text;
use Hanson\Vbot\Message\Entity\Video;
use Hanson\Vbot\Support\Console;

class MessageHandler
{
    /**
     * @var MessageHandler
     */
    static $instance = null;

    private $handler;

    private $customHandler;

    private $sync;

    private $messageFactory;

    public function __construct()
    {
        $this->sync = new Sync();
        $this->messageFactory = new MessageFactory();
    }

    /**
     * 设置单例模式
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
     * 轮询消息API接口
     */
    public function listen()
    {
        while (true){
            if($this->customHandler instanceof Closure){
                call_user_func_array($this->customHandler, []);
            }

            $time = time();
            list($retCode, $selector) = $this->sync->checkSync();

            if(in_array($retCode, ['1100', '1101'])){ # 微信客户端上登出或者其他设备登录
                break;
            }elseif ($retCode == 0){
                $this->handlerMessage($selector);
            }else{
                $this->sync->debugMessage($retCode, $selector, 10);
            }

            $this->sync->checkTime($time);
        }
    }

    /**
     * 处理消息
     *
     * @param $selector
     */
    private function handlerMessage($selector)
    {
        if($selector === 0){
            return;
        }

        $message = $this->sync->sync();

        if($message['AddMsgList']){
            foreach ($message['AddMsgList'] as $msg) {
                $content = $this->messageFactory->make($selector, $msg);
                if($content){
                    $this->addToMessageCollection($content);
                }
                if($this->handler instanceof Closure){
                    $reply = call_user_func_array($this->handler, [$content]);
                    if($reply){
                        if($reply instanceof Image){
                            Image::sendByMsgId($content->from['UserName'], $reply->msg['MsgId']);
                        }elseif($reply instanceof Video){
                            Video::sendByMsgId($content->from['UserName'], $reply->msg['MsgId']);
                        }elseif($reply instanceof Emoticon){
                            Emoticon::sendByMsgId($content->from['UserName'], $reply->msg['MsgId']);
                        }else{
                            Text::send($content->from['UserName'], $reply);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param $message Message
     */
    private function addToMessageCollection($message)
    {
        message()->put($message->msg['MsgId'], $message);

        file_put_contents(server()->config['tmp'].'/message.json', json_encode(message()->all()));
    }

}