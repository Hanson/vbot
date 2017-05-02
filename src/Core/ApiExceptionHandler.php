<?php

namespace Hanson\Vbot\Core;

use Hanson\Vbot\Exceptions\ArgumentException;

class ApiExceptionHandler
{
    public static function handle($bag, $callback = null)
    {
        if ($callback && !is_callable($callback)) {
            throw new ArgumentException();
        }

        if ($callback && $bag['BaseResponse']['Ret'] != 0) {
            call_user_func_array($callback, $bag);
        }

        switch ($bag['BaseResponse']['Ret']) {
            case 1:
                vbot('log')->error('Argument pass error.');
                break;
            case -14:
                vbot('log')->error('Ticket error.');
                break;
            case 1100:
                vbot('log')->error('Logout.');
                break;
            case 1101:
                vbot('log')->error('Logout.');
                break;
            case 1102:
                vbot('log')->error('Cookies invalid.');
                break;
            case 1105:
                vbot('log')->error('Api frequency.');
                break;
        }

        return $bag;
    }
}
