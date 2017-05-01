<?php

namespace Hanson\Vbot\Contact;

use Hanson\Vbot\Support\Content;

class Myself
{
    public $nickname;

    public $username;

    public $uin;

    public $sex;

    public $alias;

    public function init($user)
    {
        $this->nickname = Content::emojiHandle($user['NickName']);
        $this->username = $user['UserName'];
        $this->sex = $user['Sex'];
        $this->uin = $user['Uin'];

        $this->log();

        $this->setPath();
    }

    private function log()
    {
        vbot('console')->log('current user\'s nickname:'.$this->nickname);
        vbot('console')->log('current user\'s username:'.$this->username);
        vbot('console')->log('current user\'s uin:'.$this->uin);
    }

    private function setPath()
    {
        $path = vbot('config')['user_path'];

        vbot('config')['user_path'] = $path . $this->uin . DIRECTORY_SEPARATOR;

        if (!is_dir(vbot('config')['user_path']) && $this->uin) {
            mkdir(vbot('config')['user_path'], 0755, true);
        }
    }
}
