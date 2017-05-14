<?php

namespace Hanson\Vbot\Api;

class Search extends BaseApi
{
    public static function needParams(): array
    {
        return ['type'];
    }

    public static function handle($params): array
    {
        $class = '\\Hanson\\Vbot\\Contact\\'.ucfirst($params['type']);

        if (!class_exists($class)) {
            return static::response('Class: \''.$class.'\' not exist.', 500);
        }

        if ($params['type'] === 'myself') {
            return static::response('Can not get myself from \'search\'.', 500);
        }

        $type = strtolower($params['type']);

        return static::response([$type => vbot($type)->toArray(), 'count' => vbot($type)->count()], 200);
    }
}
