<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/14
 * Time: 23:08
 */

namespace Hanson\Vbot\Core;

use Closure;
use Hanson\Vbot\Message\Entity\Emoticon;
use Hanson\Vbot\Message\Entity\Image;
use Hanson\Vbot\Message\Entity\Message;
use Hanson\Vbot\Message\Entity\Text;
use Hanson\Vbot\Message\Entity\Video;
use Hanson\Vbot\Support\Console;
use Hanson\Vbot\Support\System;

class MessageHandler
{
    /**
     * @var MessageHandler
     */
    static $instance = null;

    private $handler;

    private $customHandler;

    private $exitHandler;

    private $exceptionHandler;

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
            throw new \Exception('custom handler must be a closure!');
        }

        $this->customHandler = $closure;
    }

    /**
     * 退出处理器
     *
     * @param Closure $closure
     * @throws \Exception
     */
    public function setExitHandler(Closure $closure)
    {
        if(!$closure instanceof Closure){
            throw new \Exception('exit handler must be a closure!');
        }

        $this->exitHandler = $closure;
    }

    /**
     * 异常处理器
     *
     * @param Closure $closure
     * @throws \Exception
     */
    public function setExceptionHandler(Closure $closure)
    {
        if(!$closure instanceof Closure){
            throw new \Exception('exit handler must be a closure!');
        }

        $this->exceptionHandler = $closure;
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
                Console::log('微信客户端正常退出');
                if($this->exitHandler){
                    call_user_func_array($this->exitHandler, []);
                }
                break;
            }elseif ($retCode == 0){
                $this->handlerMessage($selector);
            }else{
                Console::log('微信客户端异常退出');
                if($this->exceptionHandler){
                    call_user_func_array($this->exitHandler, []);
                }
                break;
            }

            $this->sync->checkTime($time);
        }
        Console::log('程序结束');
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
                $content = $this->messageFactory->make($msg);
                if($content){
                    $this->addToMessageCollection($content);
                    if($this->handler){
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
    }

    /**
     * @param $message Message
     */
    private function addToMessageCollection($message)
    {
        message()->put($message->msg['MsgId'], $message);

        if(server()->config['debug']) {
            $file = fopen(System::getPath() . 'message.json', 'a');
            fwrite($file, json_encode($message) . PHP_EOL);
            fclose($file);
        }
    }

}