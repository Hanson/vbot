<?php

namespace Hanson\Vbot\Core;

use Hanson\Vbot\Console\Console;
use Hanson\Vbot\Exceptions\ArgumentException;

class ApiExceptionHandler
{
    public static function handle($bag, $callback = null)
    {
        if ($callback && !is_callable($callback)) {
            throw new ArgumentException();
        }

        if ($bag['BaseResponse']['Ret'] != 0) {
            if ($callback) {
                call_user_func_array($callback, $bag);
            }
        }

        switch ($bag['BaseResponse']['Ret']) {
            case 1:
                vbot('console')->log('Argument pass error.', Console::WARNING);
                break;
            case -14:
                vbot('console')->log('Ticket error.', Console::WARNING);
                break;
            case 1100:
                vbot('console')->log('Logout.', Console::WARNING);
                break;
            case 1101:
                vbot('console')->log('Logout.', Console::WARNING);
                break;
            case 1102:
                vbot('console')->log('Cookies invalid.', Console::WARNING);
                break;
            case 1105:
                vbot('console')->log('Api frequency.', Console::WARNING);
                break;
        }

        return $bag;
    }
}
