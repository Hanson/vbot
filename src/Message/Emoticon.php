<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2017/1/10
 * Time: 16:51
 */

namespace Hanson\Robot\Message;


use Hanson\Robot\Core\Server;
use Hanson\Robot\Support\Console;

class Emoticon extends Message
{

    use UploadAble;

    public static function send($username, $file)
    {
        $response = static::uploadMedia($username, $file);

        if(!$response){
            Console::log("表情 {$file} 上传失败");
            return false;
        }

        $mediaId = $response['MediaId'];

        $url = sprintf(Server::BASE_URI . '/webwxsendemoticon?fun=sys&f=json&pass_ticket=%s' , server()->passTicket);
        $data = [
            'BaseRequest'=> server()->baseRequest,
            'Msg'=> [
                'Type'=> 47,
                "EmojiFlag"=> 2,
                'MediaId'=> $mediaId,
                'FromUserName'=> myself()->username,
                'ToUserName'=> $username,
                'LocalID'=> time() * 1e4,
                'ClientMsgId'=> time() * 1e4
            ]
        ];
        $result = http()->json($url, $data, true);

        if($result['BaseResponse']['Ret'] != 0){
            Console::log('发送表情失败');
            return false;
        }

        return true;
    }
}