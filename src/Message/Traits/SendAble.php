<?php

namespace Hanson\Vbot\Message\Traits;

use Hanson\Vbot\Core\ApiExceptionHandler;

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

        return ApiExceptionHandler::handle($result);
    }

    private static function getUrl()
    {
        return vbot('config')['server.uri.base'].DIRECTORY_SEPARATOR.static::API.'pass_ticket='.vbot('config')['server.passTicket'];
    }
}
