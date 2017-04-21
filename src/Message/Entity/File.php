<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29.
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MediaInterface;
use Hanson\Vbot\Message\MessageInterface;
use Hanson\Vbot\Message\UploadAble;
use Hanson\Vbot\Support\FileManager;

class File extends Message implements MessageInterface, MediaInterface
{
    use UploadAble;

    public $title;

    public static $folder = 'file';

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    public function make()
    {
        $array = (array) simplexml_load_string($this->message, 'SimpleXMLElement', LIBXML_NOCDATA);

        $info = (array) $array['appmsg'];

        $this->title = $info['title'];

        $this->download();
    }

    public function download()
    {
        $url = server()->fileUri.'/webwxgetmedia';
        $content = http()->get($url, [
            'sender'            => $this->raw['FromUserName'],
            'mediaid'           => $this->raw['MediaId'],
            'filename'          => $this->raw['FileName'],
            'fromuser'          => myself()->username,
            'pass_ticket'       => server()->passTicket,
            'webwx_data_ticket' => static::getTicket(),
        ]);
        FileManager::saveToUserPath(static::$folder.DIRECTORY_SEPARATOR.$this->raw['FileName'], $content);

        $this->content = '[文件]';
    }
}
