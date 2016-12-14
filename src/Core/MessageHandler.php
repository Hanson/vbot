<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/14
 * Time: 23:08
 */

namespace Hanson\Robot\Core;

use Closure;

class MessageHandler
{

    private $handler;



    public function setMessageHandler(Closure $closure)
    {
        if(!$closure instanceof Closure){
            throw new \Exception('message handler must be a closure!');
        }

        $this->handler = $closure;
    }

    public function listen()
    {
        call_user_func_array($this->handler, []);
    }

}