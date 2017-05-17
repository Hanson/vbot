<?php

namespace Hanson\Vbot\Message\Traits;

use Hanson\Vbot\Console\Console;
use Hanson\Vbot\Core\ApiExceptionHandler;
use Hanson\Vbot\Exceptions\ArgumentException;
use Hanson\Vbot\Support\File;

trait Multimedia
{
    private static $file;

    /**
     * download multimedia.
     *
     * @param $message
     * @param null $callback
     *
     * @throws ArgumentException
     *
     * @return bool
     */
    public static function download($message, $callback = null)
    {
        if (!$callback) {
            static::autoDownload($message['raw'], true);

            return true;
        }

        if ($callback && !is_callable($callback)) {
            throw new ArgumentException();
        }

        call_user_func_array($callback, [static::getResource($message['raw'])]);

        return true;
    }

    /**
     * get a resource through api.
     *
     * @param $message
     *
     * @return mixed
     */
    private static function getResource($message)
    {
        $url = static::getDownloadUrl($message);

        $content = vbot('http')->get($url, static::getDownloadOption($message));

        if (!$content) {
            vbot('console')->log('download file failed.', Console::WARNING);
        } else {
            return $content;
        }
    }

    protected static function getDownloadUrl($message)
    {
        $serverConfig = vbot('config')['server'];

        return $serverConfig['uri']['base'].DIRECTORY_SEPARATOR.static::DOWNLOAD_API."{$message['MsgId']}&skey={$serverConfig['skey']}";
    }

    protected static function getDownloadOption($message)
    {
        return [];
    }

    /**
     * download resource to a default path.
     *
     * @param $message
     * @param bool $force
     */
    protected static function autoDownload($message, $force = false)
    {
        $isDownload = vbot('config')['download.'.static::TYPE];

        if ($isDownload || $force) {
            $resource = static::getResource($message);

            if ($resource) {
                File::saveTo(vbot('config')['user_path'].static::TYPE.DIRECTORY_SEPARATOR.
                    static::fileName($message), $resource);
            }
        }
    }

    protected static function fileName($message)
    {
        return $message['MsgId'].static::EXT;
    }

    protected static function getDefaultFile($message)
    {
        return vbot('config')['user_path'].static::TYPE.DIRECTORY_SEPARATOR.static::fileName($message);
    }

    /**
     * @param $username
     * @param $file
     *
     * @return bool|mixed|string
     */
    public static function uploadMedia($username, $file)
    {
        if (!is_file($file)) {
            return false;
        }

        $url = vbot('config')['server.uri.file'].'/webwxuploadmedia?f=json';

        static::$file = $file;
        list($mime, $mediaType) = static::getMediaType($file);

        $data = [
            'id'                 => 'WU_FILE_0',
            'name'               => basename($file),
            'type'               => $mime,
            'lastModifieDate'    => gmdate('D M d Y H:i:s TO', filemtime($file)).' (CST)',
            'size'               => filesize($file),
            'mediatype'          => $mediaType,
            'uploadmediarequest' => json_encode([
                'BaseRequest'   => vbot('config')['server.baseRequest'],
                'ClientMediaId' => time(),
                'TotalLen'      => filesize($file),
                'StartPos'      => 0,
                'DataLen'       => filesize($file),
                'MediaType'     => 4,
                'UploadType'    => 2,
                'FromUserName'  => vbot('myself')->username,
                'ToUserName'    => $username,
                'FileMd5'       => md5_file($file),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'webwx_data_ticket' => static::getTicket(),
            'pass_ticket'       => vbot('config')['server.passTicket'],
            'filename'          => fopen($file, 'r'),
        ];

        $data = static::dataToMultipart($data);

        $result = vbot('http')->request($url, 'post', [
            'multipart' => $data,
        ]);
        $result = json_decode($result, true);

        return ApiExceptionHandler::handle($result);
    }

    /**
     * 获取媒体类型.
     *
     * @param $file
     *
     * @return array
     */
    private static function getMediaType($file)
    {
        $info = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($info, $file);
        finfo_close($info);

        $fileExplode = explode('.', $file);
        $fileExtension = end($fileExplode);

        return [$mime, $fileExtension === 'jpg' ? 'pic' : ($fileExtension === 'mp4' ? 'video' : 'doc')];
    }

    /**
     * 获取cookie的ticket.
     *
     * @return mixed
     */
    private static function getTicket()
    {
        $cookies = vbot('http')->getClient()->getConfig('cookies')->toArray();

        $key = array_search('webwx_data_ticket', array_column($cookies, 'Name'));

        return $cookies[$key]['Value'];
    }

    /**
     * 把请求数组转为multipart模式.
     *
     * @param $data
     *
     * @return array
     */
    private static function dataToMultipart($data)
    {
        $result = [];

        foreach ($data as $key => $item) {
            $field = [
                'name'     => $key,
                'contents' => $item,
            ];
            if ($key === 'filename') {
                $field['filename'] = basename(static::$file);
            }
            $result[] = $field;
        }

        return $result;
    }
}
