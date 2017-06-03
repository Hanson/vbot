<?php

use Hanson\Vbot\Foundation\Vbot;

if (!function_exists('vbot')) {
    /**
     * Get the available container instance.
     *
     * @param string $abstract
     * @param array  $parameters
     *
     * @return mixed|Vbot
     */
    function vbot($abstract = null, array $parameters = [])
    {
        if (is_null($abstract)) {
            return Vbot::getInstance();
        }

        return empty($parameters)
            ? Vbot::getInstance()->make($abstract)
            : Vbot::getInstance()->makeWith($abstract, $parameters);
    }
}
