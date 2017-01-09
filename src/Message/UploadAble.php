<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2017/1/9
 * Time: 16:18
 */

namespace Hanson\Robot\Message;


/**
 * Class UploadAble
 * @package Hanson\Robot\Message\
 *
 * @property string $mediaCount
 */
trait UploadAble
{

    /**
     * @param $username
     * @param $file
     */
    public function uploadMedia($username, $file)
    {
        $url = 'https://file.wx.qq.com/cgi-bin/mmwebwx-bin/webwxuploadmedia?f=json';
        $this->mediaCount = ++$this->mediaCount;

        $lastModifyDate = gmdate('D M d Y H:i:s TO', filemtime($file));
        list($mime, $mediaType) = $this->getMediaType($file);

        http()->post($url, [
            'id' => 'WU_FILE_' .$this->mediaCount,
            'name' => $file,
            'type' => $mime,
            'lastModifieDate' => $lastModifyDate,
            'size' => filesize($file),
            'mediatype' => $mediaType,
            'uploadmediarequest' => json_encode([
                'BaseRequest' => server()->baseRequest,
                'ClientMediaId' => (time() * 1000).mt_rand(10000,99999),
                'TotalLen' => filesize($file),
                'StartPos' => 0,
                'DataLen' => filesize($file),
                'MediaType' => 4,
                'UploadType' => 2,
                'FromUserName' => myself()->username,
                'ToUserName' => $username,
                'FileMd5' => md5_file($file)
            ],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES),
            'webwx_data_ticket' => http()->getClient()->get,
            'pass_ticket' => $pass_ticket,
            'filename' => '@'.$file_name
        ]);
    }

    private function getMediaType($file)
    {
        $info = finfo_open(FILEINFO_MIME_TYPE);
        $mime =  finfo_file($info, $file);
        finfo_close($info);

        return [$mime, explode('/', $mime)[0] === 'image' ? 'pic' : 'doc'];
    }
}