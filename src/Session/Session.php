<?php

namespace Hanson\Vbot\Session;

class Session
{
    public static function randomKey()
    {
        return bin2hex(random_bytes(3));
    }

    public static function currentSession()
    {
        $arguments = getopt(null, ['session::']);

        return isset($arguments['session']) && $arguments['session'] ? $arguments['session'] : self::randomKey();
    }
}
