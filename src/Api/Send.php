<?php


namespace Hanson\Vbot\Api;


use Hanson\Vbot\Message\Traits\SendAble;

class Send extends BaseApi
{

    public static function needParams(): array
    {
        return ['type', 'username', 'content'];
    }

    public static function handle($params): array
    {
        /** @var SendAble $class */
        $class = '\\Hanson\\Vbot\\Message\\'.ucfirst($params['type']);

        if (!class_exists($class)) {
            return static::response('Class: ' . $class . ' not exist.', 500);
        }

        if (!method_exists(new $class(), 'send')) {
            return static::response('Class: ' . $class . ' doesn\'t support send.', 500);
        }

        $params = array_merge([$params['username']], explode(',', $params['content']));

        return static::response($class::send(...$params), 200);
    }
}