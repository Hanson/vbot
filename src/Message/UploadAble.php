<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2017/1/9
 * Time: 16:18.
 */

namespace Hanson\Vbot\Message;

use Hanson\Vbot\Core\Server;
use Hanson\Vbot\Support\Console;

/**
 * Class UploadAble.
 *
 * @property  string static $mediaCount
 */
trait UploadAble
{
    public static $file;

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

        $url = server()->fileUri.'/webwxuploadmedia?f=json';
        static::$mediaCount = ++static::$mediaCount;
        static::$file = $file;

        list($mime, $mediaType) = static::getMediaType($file);

        $data = [
            'id'                 => 'WU_FILE_'.static::$mediaCount,
            'name'               => basename($file),
            'type'               => $mime,
            'lastModifieDate'    => gmdate('D M d Y H:i:s TO', filemtime($file)).' (CST)',
            'size'               => filesize($file),
            'mediatype'          => $mediaType,
            'uploadmediarequest' => json_encode([
                'BaseRequest'   => server()->baseRequest,
                'ClientMediaId' => time(),
                'TotalLen'      => filesize($file),
                'StartPos'      => 0,
                'DataLen'       => filesize($file),
                'MediaType'     => 4,
                'UploadType'    => 2,
                'FromUserName'  => myself()->username,
                'ToUserName'    => $username,
                'FileMd5'       => md5_file($file),
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'webwx_data_ticket' => static::getTicket(),
            'pass_ticket'       => (server()->passTicket),
            'filename'          => fopen($file, 'r'),
        ];

        $data = static::dataToMultipart($data);

        $result = http()->request($url, 'post', [
            'multipart' => $data,
        ]);
        $result = json_decode($result, true);

        if ($result['BaseResponse']['Ret'] == 0) {
            return $result;
        }

        return false;
    }

    public static function send($username, $file)
    {
        $response = static::uploadMedia($username, $file);

        if (!$response) {
            Console::log("文件 {$file} 上传失败", Console::WARNING);

            return false;
        }

        $mediaId = $response['MediaId'];

        $url = server()->baseUri.'/webwxsendappmsg?fun=async&f=json&pass_ticket='.server()->passTicket;
        $explode = explode('.', $file);
        $fileName = end($explode);
        $data = [
            'BaseRequest'=> server()->baseRequest,
            'Msg'        => [
                'Type'        => 6,
                'Content'     => sprintf("<appmsg appid='wxeb7ec651dd0aefa9' sdkver=''><title>%s</title><des></des><action></action><type>6</type><content></content><url></url><lowurl></lowurl><appattach><totallen>%s</totallen><attachid>%s</attachid><fileext>%s</fileext></appattach><extinfo></extinfo></appmsg>", basename($file), filesize($file), $mediaId, $fileName),
                'FromUserName'=> myself()->username,
                'ToUserName'  => $username,
                'LocalID'     => time() * 1e4,
                'ClientMsgId' => time() * 1e4,
            ],
        ];
        $result = http()->post($url, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), true);

        if ($result['BaseResponse']['Ret'] != 0) {
            Console::log('发送文件失败', Console::WARNING);

            return false;
        }

        return true;
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
        $cookies = http()->getClient()->getConfig('cookies')->toArray();

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
