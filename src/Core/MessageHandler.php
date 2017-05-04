<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/14
 * Time: 23:08.
 */

namespace Hanson\Vbot\Core;

use Carbon\Carbon;
use Closure;
use Hanson\Vbot\Message\Entity\Emoticon;
use Hanson\Vbot\Message\Entity\Image;
use Hanson\Vbot\Message\Entity\Message;
use Hanson\Vbot\Message\Entity\Text;
use Hanson\Vbot\Message\Entity\Video;
use Hanson\Vbot\Support\Console;
use Hanson\Vbot\Support\Path;

class MessageHandler
{
    /**
     * @var MessageHandler
     */
    public static $instance = null;

    private $handler;

    private $customHandler;

    private $exitHandler;

    private $exceptionHandler;

    private $onceHandler;

    private $sync;

    private $messageFactory;

    public function __construct()
    {
        $this->sync = new Sync();
        $this->messageFactory = new MessageFactory();
    }

    /**
     * 设置单例模式.
     *
     * @return MessageHandler
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * 消息处理器.
     *
     * @param Closure $closure
     *
     * @throws \Exception
     */
    public function setMessageHandler(Closure $closure)
    {
        if (!$closure instanceof Closure) {
            throw new \Exception('message handler must be a closure!');
        }

        $this->handler = $closure;
    }

    /**
     * 自定义处理器.
     *
     * @param Closure $closure
     *
     * @throws \Exception
     */
    public function setCustomHandler(Closure $closure)
    {
        if (!$closure instanceof Closure) {
            throw new \Exception('custom handler must be a closure!');
        }

        $this->customHandler = $closure;
    }

    /**
     * 退出处理器.
     *
     * @param Closure $closure
     *
     * @throws \Exception
     */
    public function setExitHandler(Closure $closure)
    {
        if (!$closure instanceof Closure) {
            throw new \Exception('exit handler must be a closure!');
        }

        $this->exitHandler = $closure;
    }

    /**
     * 异常处理器.
     *
     * @param Closure $closure
     *
     * @throws \Exception
     */
    public function setExceptionHandler(Closure $closure)
    {
        if (!$closure instanceof Closure) {
            throw new \Exception('exit handler must be a closure!');
        }

        $this->exceptionHandler = $closure;
    }

    /**
     * 执行一次的处理器.
     *
     * @param Closure $closure
     *
     * @throws \Exception
     */
    public function setOnceHandler(Closure $closure)
    {
        if (!$closure instanceof Closure) {
            throw new \Exception('exit handler must be a closure!');
        }

        $this->onceHandler = $closure;
    }

    /**
     * 轮询消息API接口.
     */
    public function listen()
    {
        if ($this->onceHandler instanceof Closure) {
            call_user_func_array($this->onceHandler, []);
        }

        $time = 0;

        while (true) {
            if ($this->customHandler instanceof Closure) {
                call_user_func_array($this->customHandler, []);
            }

            if (time() - $time > 1800) {
                Text::send('filehelper', '心跳 '.Carbon::now()->toDateTimeString());
                $time = time();
            }

            list($retCode, $selector) = $this->sync->checkSync();

            if (!$this->handleCheckSync($retCode, $selector)) {
                break;
            }
        }
        Console::log('程序结束');
    }

    public function handleCheckSync($retCode, $selector, $test = false)
    {
        if (in_array($retCode, ['1100', '1101'])) { // 微信客户端上登出或者其他设备登录
            Console::log('微信客户端正常退出');
            if ($this->exitHandler) {
                call_user_func_array($this->exitHandler, []);
            }

            return false;
        } elseif ($retCode == 0) {
            if (!$test) {
                $this->handlerMessage($selector);
            }

            return true;
        } else {
            Console::log('微信客户端异常退出');
            if ($this->exceptionHandler) {
                call_user_func_array($this->exitHandler, []);
            }

            return false;
        }
    }

    /**
     * 处理消息.
     *
     * @param $selector
     */
    private function handlerMessage($selector)
    {
        if ($selector === 0) {
            return;
        }

        $message = $this->sync->sync();

        if (!$message) {
            return;
        }

        if (count($message['ModContactList']) > 0) {
            foreach ($message['ModContactList'] as $contact) {
                if (str_contains($contact['UserName'], '@@')) {
                    group()->put($contact['UserName'], $contact);
                } else {
                    contact()->put($contact['UserName'], $contact);
                }
            }
        }

        if ($message['AddMsgList']) {
            foreach ($message['AddMsgList'] as $msg) {
                $content = $this->messageFactory->make($msg);
                if ($content) {
                    $this->debugMessage($content);
                    $this->addToMessageCollection($content);
                    if ($this->handler) {
                        $reply = call_user_func_array($this->handler, [$content]);
                        if ($reply) {
                            if ($reply instanceof Image) {
                                Image::sendByMsgId($content->from['UserName'], $reply->raw['MsgId']);
                            } elseif ($reply instanceof Video) {
                                Video::sendByMsgId($content->from['UserName'], $reply->raw['MsgId']);
                            } elseif ($reply instanceof Emoticon) {
                                Emoticon::sendByMsgId($content->from['UserName'], $reply->raw['MsgId']);
                            } else {
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
        message()->put($message->raw['MsgId'], $message);

        foreach (message()->all() as $msgId => $item) {
            if ($item->raw['CreateTime'] + 120 < time()) {
                message()->pull($msgId);
            } else {
                break;
            }
        }

        if (server()->config['debug']) {
            $file = fopen(Path::getCurrentUinPath().'message.json', 'a');
            fwrite($file, json_encode($message).PHP_EOL);
            fclose($file);
        }
    }

    /**
     * debug出消息.
     *
     * @param $content
     */
    private function debugMessage(Message $content)
    {
        if (server()->config['debug']) {
            Console::log("[{$content->raw['MsgId']}] ".$content->content, Console::MESSAGE);
        }
    }
}
