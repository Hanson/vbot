<?php

namespace Hanson\Vbot\Example\Modules;

use Hanson\Vbot\Message\Image;
use Hanson\Vbot\Message\Text;
use Hanson\Vbot\Message\Video;
use Hanson\Vbot\Message\Voice;
use Illuminate\Support\Collection;

class MessageModule
{
    public static function messageHandler(Collection $message)
    {
        if($message['from']['NickName'] === 'HanSon'){
            if($message['type'] === 'text' && $message['content'] === 'hi'){
                Text::send($message['from']['UserName'], 'hi');
            }

            if($message['type'] === 'location'){
                Text::send($message['from']['UserName'], $message['content']);
                Text::send($message['from']['UserName'], $message['url']);
            }

            if($message['type'] === 'new_friend'){
                Text::send($message['from']['UserName'], $message['content']);
            }

            if($message['type'] === 'image'){
                Image::download($message);
                Image::download($message, function($resource){
                    file_put_contents(__DIR__.'/test1.jpg', $resource);
                });
                Image::send($message['from']['UserName'], $message);
                Image::send($message['from']['UserName'], __DIR__.'/test1.jpg');
            }

            // todo
            if($message['type'] === 'voice'){
                Voice::download($message);
                Voice::download($message, function($resource){
                    file_put_contents(__DIR__.'/test1.mp3', $resource);
                });
                Voice::send($message['from']['UserName'], $message);
                Voice::send($message['from']['UserName'], __DIR__.'/test1.mp3');
            }

            if($message['type'] === 'video'){
//                Video::download($message);
//                Video::download($message, function($resource){
//                    file_put_contents(__DIR__.'/test1.mp4', $resource);
//                });
                Video::send($message['from']['UserName'], $message);
//                Video::send($message['from']['UserName'], __DIR__.'/test1.mp4');
            }

        }

    }
}
