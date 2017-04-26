<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/13
 * Time: 22:08.
 */

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Message\ResourceInterface;
use Hanson\Vbot\Message\MediaTrait;
use Hanson\Vbot\Message\MessageInterface;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\UploadAble;
use Hanson\Vbot\Support\FileManager;

class Voice extends Message implements MessageInterface, ResourceInterface
{
    use UploadAble, MediaTrait;

    public static $folder = 'mp3';

    public function __construct(Vbot $vbot)
    {
        parent::__construct($vbot);

        $this->make();
    }

    /**
     * 下载文件.
     *
     * @return mixed
     */
    public function download()
    {
        $url = server()->baseUri.sprintf('/webwxgetvoice?msgid=%s&skey=%s', $this->raw['MsgId'], server()->skey);
        $content = http()->get($url);
        FileManager::saveToUserPath(static::$folder.DIRECTORY_SEPARATOR.$this->raw['MsgId'].'.mp3', $content);
    }

    public function make()
    {
        $this->download();
    }
}
