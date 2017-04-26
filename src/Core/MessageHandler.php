<?php

namespace Hanson\Vbot\Core;

use Carbon\Carbon;
use Hanson\Vbot\Foundation\Vbot;

class MessageHandler
{
    /**
     * @var Vbot
     */
    protected $vbot;

    protected $messageHandler;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    public function listen()
    {
        $this->vbot->beforeMessageObserver->trigger();

        $time = 0;

        while (true) {
            if (time() - $time > 1800) {
                Text::send('filehelper', '心跳 '.Carbon::now()->toDateTimeString());
                $time = time();
            }

            list($retCode, $selector) = $this->vbot->sync->checkSync();

            if (!$this->handleCheckSync($retCode, $selector)) {
                break;
            }
        }
    }

    public function handleCheckSync($retCode, $selector, $test = false)
    {
        if (in_array($retCode, ['1100', '1101'])) { // 微信客户端上登出或者其他设备登录
            $this->vbot->console->log('vbot exit normally.');

            return false;
        } elseif ($retCode == 0) {
            if (!$test) {
                $this->handlerMessage($selector);
            }

            return true;
        } else {
            $this->vbot->console->log('vbot exit unexpected.');

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

        $message = $this->vbot->sync->sync();

        if (count($message['ModContactList']) > 0) {
            $this->vbot->contactFactory->store($message['ModContactList']);
        }

        if ($message['AddMsgList']) {
            foreach ($message['AddMsgList'] as $msg) {
                $content = $this->vbot->messageFactory->make($msg);
                if ($content) {
                    $this->debugMessage($content);
                    $this->addToMessageCollection($content);
                    if ($this->handler) {
                        call_user_func_array($this->handler, [$content]);
                    }
                }
            }
        }
    }
}
