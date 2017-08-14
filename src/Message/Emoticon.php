<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2017/1/10
 * Time: 16:51.
 */

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Console\Console;
use Hanson\Vbot\Message\Traits\Multimedia;
use Hanson\Vbot\Message\Traits\SendAble;
use Hanson\Vbot\Support\File;

class Emoticon extends Message implements MessageInterface
{
    use SendAble, Multimedia;

    const API = 'webwxsendemoticon?fun=sys&f=json&';
    const DOWNLOAD_API = 'webwxgetmsgimg?&MsgID=';
    const EXT = '.gif';
    const TYPE = 'emoticon';

    public function make($msg)
    {
        static::autoDownload($msg);
        static::downloadToLibrary($msg);

        return $this->getCollection($msg, static::TYPE);
    }

    protected function parseToContent(): string
    {
        return '[表情]';
    }

    public static function send($username, $mix)
    {
        $file = is_string($mix) ? $mix : static::getDefaultFile($mix['raw']);

        if (!is_file($file)) {
            return false;
        }

        $response = static::uploadMedia($username, $file);

        return static::sendMsg([
            'Type'         => 47,
            'EmojiFlag'    => 2,
            'MediaId'      => $response['MediaId'],
            'FromUserName' => vbot('myself')->username,
            'ToUserName'   => $username,
            'LocalID'      => time() * 1e4,
            'ClientMsgId'  => time() * 1e4,
        ]);
    }

    /**
     * 从本地表�
     * 库随机发送一个.
     *
     * @param $username
     *
     * @return bool
     */
    public static function sendRandom($username)
    {
        if (!is_dir($path = vbot('config')['download.emoticon_path'])) {
            vbot('console')->log('emoticon path not set.', Console::WARNING);

            return false;
        }

        $files = scandir($path);
        unset($files[0], $files[1]);

        if (count($files)) {
            $msgId = $files[array_rand($files)];

            static::send($username, $path.DIRECTORY_SEPARATOR.$msgId);
        }
    }

    private static function downloadToLibrary($message)
    {
        if (!vbot('config')['download.emoticon_path']) {
            return false;
        }

        if (is_file($path = vbot('config')['user_path'].static::TYPE.DIRECTORY_SEPARATOR.$message['MsgId'].static::EXT)) {
            static::copyFromEmoticon($path);
        } else {
            static::saveFromApi($message);
        }
    }

    private static function copyFromEmoticon($path)
    {
        $target = vbot('config')['download.emoticon_path'].DIRECTORY_SEPARATOR;

        if (!static::isExist($md5 = md5_file($path))) {
            copy($path, $target.$md5.static::EXT);
        }
    }

    private static function saveFromApi($message)
    {
        $target = vbot('config')['download.emoticon_path'].DIRECTORY_SEPARATOR;

        $resource = static::getResource($message);

        $fileName = $target.'tmp-'.time().rand().static::EXT;

        File::saveTo($fileName, $resource);

        $md5 = md5_file($fileName);
        copy($fileName, $target.$md5.static::EXT);
        unlink($fileName);
    }

    private static function isExist($md5)
    {
        return is_file(vbot('config')['download.emoticon_path'].DIRECTORY_SEPARATOR.$md5.static::EXT);
    }
}
