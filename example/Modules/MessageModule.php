<?php

namespace Hanson\Vbot\Example\Modules;

use Hanson\Vbot\Message\MessageInterface;
use Hanson\Vbot\Message\Text;

class MessageModule
{
    public static function messageHandler(MessageInterface $message)
    {
        if ($message instanceof Text) {
            $text = new Text('', '');
            $text->send('', '');
//            Text::send('filehelper', 'hi');
        }
    }
}
