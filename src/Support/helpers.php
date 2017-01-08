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
use Hanson\Robot\Collections\Account;
use Hanson\Robot\Collections\Member;
use Hanson\Robot\Collections\Contact;
use Hanson\Robot\Collections\Message;
use Hanson\Robot\Collections\Group;

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
if (! function_exists('account')) {
    /**
     * Get the available container instance.
     *
     * @return Account
     */
    function account()
    {
        return Account::getInstance();
    }
}
if (! function_exists('contact')) {
    /**
     * Get the available container instance.
     *
     * @return Contact
     */
    function contact()
    {
        return Contact::getInstance();
    }
}
if (! function_exists('member')) {
    /**
     * Get the available container instance.
     *
     * @return Member
     */
    function member()
    {
        return Member::getInstance();
    }
}
if (! function_exists('group')) {
    /**
     * Get the available container instance.
     *
     * @return Group
     */
    function group()
    {
        return Group::getInstance();
    }
}
if (! function_exists('message')) {
    /**
     * Get the available container instance.
     *
     * @return Message
     */
    function message()
    {
        return Message::getInstance();
    }
}