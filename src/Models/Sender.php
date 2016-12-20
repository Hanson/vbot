<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/17
 * Time: 14:58
 */

namespace Hanson\Robot\Models;


class Sender
{

    /**
     * @var String 消息来源名称
     */
    public $name;

    public $type;

    public $from;

    public $to;

    /**
     * @var array 显示的聊天窗口
     */
    public $contact;
}