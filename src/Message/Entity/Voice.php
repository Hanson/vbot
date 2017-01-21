<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/13
 * Time: 22:08
 */

namespace Hanson\Vbot\Message\Entity;


use Hanson\Vbot\Message\MediaInterface;
use Hanson\Vbot\Message\MediaTrait;
use Hanson\Vbot\Message\MessageInterface;
use Hanson\Vbot\Message\UploadAble;
use Hanson\Vbot\Support\FileManager;

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
        $this->download();
    }
}
