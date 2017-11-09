<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29.
 */

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Traits\Multimedia;
use Hanson\Vbot\Message\Traits\SendAble;

class File extends Message implements MessageInterface
{
    use Multimedia, SendAble;

    const API = 'webwxsendappmsg?fun=async&f=json&';
    const DOWNLOAD_API = 'webwxgetmedia';
    const TYPE = 'file';

    private $title;

    public function make($msg)
    {
        static::autoDownload($msg);

        return $this->getCollection($msg, static::TYPE);
    }

    protected function getExpand():array
    {
        return ['title' => $this->title];
    }

    protected function afterCreate()
    {
        $array = (array) simplexml_load_string($this->message, 'SimpleXMLElement', LIBXML_NOCDATA);

        $info = (array) $array['appmsg'];

        $this->title = $info['title'];
    }

    public static function send($username, $mix)
    {
        $file = is_string($mix) ? $mix : static::getDefaultFile($mix['raw']);

        $response = static::uploadMedia($username, $file);

        $mediaId = $response['MediaId'];

        $explode = explode('.', $file);
        $fileName = end($explode);

        return static::sendMsg([
            'Type'         => 6,
            'Content'      => sprintf("<appmsg appid='wxeb7ec651dd0aefa9' sdkver=''><title>%s</title><des></des><action></action><type>6</type><content></content><url></url><lowurl></lowurl><appattach><totallen>%s</totallen><attachid>%s</attachid><fileext>%s</fileext></appattach><extinfo></extinfo></appmsg>", basename($file), filesize($file), $mediaId, $fileName),
            'FromUserName' => vbot('myself')->username,
            'ToUserName'   => $username,
            'LocalID'      => time() * 1e4,
            'ClientMsgId'  => time() * 1e4,
        ]);
    }

    protected static function getDownloadUrl($message)
    {
        $serverConfig = vbot('config')['server'];

        return $serverConfig['uri']['file'].DIRECTORY_SEPARATOR.static::DOWNLOAD_API;
    }

    protected static function getDownloadOption($msg)
    {
        return ['query' => [
                'sender'            => $msg['FromUserName'],
                'mediaid'           => $msg['MediaId'],
                'filename'          => $msg['FileName'],
                'fromuser'          => vbot('myself')->username,
                'pass_ticket'       => vbot('config')['server.passTicket'],
                'webwx_data_ticket' => static::getTicket(),
            ],
        ];
    }

    protected static function fileName($message)
    {
        return $message['FileName'];
    }

    protected function parseToContent(): string
    {
        return '[文件]'.$this->title;
    }
}
