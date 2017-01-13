<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/13
 * Time: 22:08
 */

namespace Hanson\Robot\Message;


use Hanson\Robot\Core\Server;
use Hanson\Robot\Support\Console;

class Video extends Message
{
    use UploadAble;

    public static function send($username, $file)
    {
        $response = static::uploadMedia($username, $file);

        if(!$response){
            Console::log("视频 {$file} 上传失败");
            return false;
        }

        $mediaId = $response['MediaId'];

        $url = sprintf(Server::BASE_URI . '/webwxsendvideomsg?fun=async&f=json&pass_ticket=%s' , server()->passTicket);
        $data = [
            'BaseRequest'=> server()->baseRequest,
            'Msg'=> [
                'Type'=> 43,
                'MediaId'=> $mediaId,
                'FromUserName'=> myself()->username,
                'ToUserName'=> $username,
                'LocalID'=> time() * 1e4,
                'ClientMsgId'=> time() * 1e4
            ]
        ];
        $result = http()->json($url, $data, true);

        if($result['BaseResponse']['Ret'] != 0){
            Console::log('发送视频失败');
            return false;
        }

        return true;
    }
}