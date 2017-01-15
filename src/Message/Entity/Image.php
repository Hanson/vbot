<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2017/1/10
 * Time: 16:51
 */

namespace Hanson\Robot\Message\Entity;


use Hanson\Robot\Support\Console;
use Hanson\Robot\Support\FileManager;
use Hanson\Robot\Message\MediaInterface;
use Hanson\Robot\Message\MediaTrait;
use Hanson\Robot\Message\MessageInterface;
use Hanson\Robot\Message\UploadAble;

class Image extends Message implements MessageInterface, MediaInterface
{

    use UploadAble, MediaTrait;

    static $folder = 'jpg';

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    public static function sendByMsgId($username, $msgId)
    {
        $path = static::getPath(static::$folder);

        static::send($username, $path . "/{$msgId}.jpg");
    }

    public static function send($username, $file)
    {
        $response = static::uploadMedia($username, $file);

        if(!$response){
            Console::log("文件 {$file} 上传失败");
            return false;
        }

        $mediaId = $response['MediaId'];

        $url = sprintf(server()->baseUri . '/webwxsendmsgimg?fun=async&f=json&pass_ticket=%s' , server()->passTicket);
        $data = [
            'BaseRequest'=> server()->baseRequest,
            'Msg'=> [
                'Type'=> 3,
                'MediaId'=> $mediaId,
                'FromUserName'=> myself()->username,
                'ToUserName'=> $username,
                'LocalID'=> time() * 1e4,
                'ClientMsgId'=> time() * 1e4
            ]
        ];
        $result = http()->json($url, $data, true);

        if($result['BaseResponse']['Ret'] != 0){
            Console::log('发送图片失败');
            return false;
        }

        return true;
    }

    public function make()
    {
        $this->download();
    }

    public function download()
    {
        $url = server()->baseUri . sprintf('/webwxgetmsgimg?MsgID=%s&skey=%s', $this->msg['MsgId'], server()->skey);
        $content = http()->get($url);
        FileManager::download($this->msg['MsgId'].'.jpg', $content, static::$folder);
    }
}