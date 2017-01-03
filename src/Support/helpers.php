<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/29
 * Time: 0:10
 */

use Hanson\Robot\Core\Server;
use Hanson\Robot\Core\Myself;
use Hanson\Robot\Core\Http;

if (! function_exists('server')) {
    /**
     * Get the available container instance.
     *
     * @param  array  $config
     * @return Server
     */
    function server($config = [])
    {
        return Server::getInstance($config);
    }
}
if (! function_exists('myself')) {
    /**
     * Get the available container instance.
     *
     * @return Myself
     */
    function myself()
    {
        return Myself::getInstance();
    }
}
if (! function_exists('http')) {
    /**
     * Get the available container instance.
     *
     * @return Http
     */
    function http()
    {
        return Http::getInstance();
    }
}