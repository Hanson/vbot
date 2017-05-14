<?php

namespace Hanson\Vbot\Api;

abstract class BaseApi
{
    public static function validate($params)
    {
        if ($diff = array_diff(static::needParams(), array_keys($params))) {
            return static::response('params : \''.implode('\', \'', $diff).'\' missing.', 500);
        }

        return true;
    }

    protected static function response($result = [], $code = 200):array
    {
        return ['code' => $code, 'result' => $result];
    }

    public static function execute($params):array
    {
        if (is_array($result = static::validate($params))) {
            return $result;
        }

        return static::handle($params);
    }

    abstract public static function needParams():array;

    abstract public static function handle($params):array;
}
