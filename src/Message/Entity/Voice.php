<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/13
 * Time: 22:08
 */

namespace Hanson\Robot\Message\Entity;


use Hanson\Robot\Message\MediaInterface;
use Hanson\Robot\Message\MediaTrait;
use Hanson\Robot\Message\MessageInterface;
use Hanson\Robot\Message\UploadAble;
use Hanson\Robot\Support\FileManager;

class Voice extends Message implements MessageInterface, MediaInterface
{
    use UploadAble, MediaTrait;

    static $folder = 'mp3';

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    /**
     * 根据MsgID发送文件
     *
     * @param $username
     * @param $msgId
     * @return mixed
     */
    public static function sendByMsgId($username, $msgId)
    {
    }

    /**
     * 下载文件
     *
     * @return mixed
     */
    public function download()
    {
        $url = server()->baseUri . sprintf('/webwxgetvoice?msgid=%s&skey=%s', $this->msg['MsgId'], server()->skey);
        $content = http()->get($url);
        FileManager::download($this->msg['MsgId'].'.mp3', $content, static::$folder);
    }

    public function make()
    {
    }
}