<?php

namespace Hanson\Vbot\Message\Traits;

use Hanson\Vbot\Core\ApiExceptionHandler;
use Hanson\Vbot\Message\Text;

/**
 * Trait SendAble.
 */
trait SendAble
{
    protected static function sendMsg($msg)
    {
        $data = [
            'BaseRequest' => vbot('config')['server.baseRequest'],
            'Msg'         => $msg,
            'Scene'       => 0,
        ];

        $result = vbot('http')->post(static::getUrl(),
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), true
        );

        static::stopSync();

        sleep(1);

        return ApiExceptionHandler::handle($result);
    }

    private static function getUrl()
    {
        return vbot('config')['server.uri.base'].'/'.static::API.'pass_ticket='.vbot('config')['server.passTicket'];
    }

    private static function stopSync()
    {
        if (get_class(new static()) != Text::class) {
            Text::send('filehelper', 'stop sync');
        }
    }

    abstract public static function send(...$args);
}
