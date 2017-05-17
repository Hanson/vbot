<?php

namespace Hanson\Vbot\Example\Handlers\Type;

use Hanson\Vbot\Contact\Friends;
use Hanson\Vbot\Contact\Groups;
use Hanson\Vbot\Message\Emoticon;
use Hanson\Vbot\Message\Image;
use Hanson\Vbot\Message\Text;
use Hanson\Vbot\Message\Video;
use Hanson\Vbot\Message\Voice;
use Hanson\Vbot\Support\File;
use Illuminate\Support\Collection;

class RecallType
{
    public static function messageHandler(Collection $message)
    {
        if ($message['type'] === 'recall') {
            Text::send($message['from']['UserName'], $message['content'] . ' : ' . $message['origin']['content']);
            if ($message['origin']['type'] === 'image') {
                Image::send($message['from']['UserName'], $message['origin']);
            } elseif ($message['origin']['type'] === 'emoticon') {
                Emoticon::send($message['from']['UserName'], $message['origin']);
            } elseif ($message['origin']['type'] === 'video') {
                Video::send($message['from']['UserName'], $message['origin']);
            } elseif ($message['origin']['type'] === 'voice') {
                Voice::send($message['from']['UserName'], $message['origin']);
            }
        }
    }
}
