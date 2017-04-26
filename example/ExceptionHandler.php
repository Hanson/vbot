<?php

namespace Hanson\Vbot\Example;

use Exception;
use Hanson\Vbot\Exceptions\LoginTimeoutException;
use Hanson\Vbot\Exceptions\SyncCheckException;

class ExceptionHandler
{
    public static function handler(Exception $e)
    {
        if ($e instanceof SyncCheckException) {
            echo $e->getMessage();
        } elseif ($e instanceof LoginTimeoutException) {
            echo $e->getMessage();
        }

        return true;
    }
}
