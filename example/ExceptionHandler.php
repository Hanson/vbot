<?php

namespace Hanson\Vbot\Example;

use Exception;
use Hanson\Vbot\Exceptions\LoginTimeoutException;

class ExceptionHandler
{
    public static function handler(Exception $e)
    {
        if ($e instanceof LoginTimeoutException) {
            echo $e->getMessage();
        }

        return true;
    }
}
