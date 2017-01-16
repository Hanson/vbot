<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 2:53
 */

namespace Hanson\Vbot\Message;


interface MediaInterface
{

    /**
     * 根据MsgID发送文件
     *
     * @param $username
     * @param $msgId
     * @return mixed
     */
    public static function sendByMsgId($username, $msgId);

    /**
     * 下载文件
     *
     * @return mixed
     */
    public function download();

}