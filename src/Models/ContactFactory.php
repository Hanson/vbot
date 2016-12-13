<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/12
 * Time: 20:41
 */

namespace Hanson\Robot\Models;


use Hanson\Robot\Core\Server;
use Hanson\Robot\Support\Log;

class ContactFactory
{
    protected $server;

    const SPECIAL_USERS = ['newsapp', 'fmessage', 'filehelper', 'weibo', 'qqmail',
        'fmessage', 'tmessage', 'qmessage', 'qqsync', 'floatbottle',
        'lbsapp', 'shakeapp', 'medianote', 'qqfriend', 'readerapp',
        'blogapp', 'facebookapp', 'masssendapp', 'meishiapp',
        'feedsapp', 'voip', 'blogappweixin', 'weixin', 'brandsessionholder',
        'weixinreminder', 'wxid_novlwrv3lqwv11', 'gh_22b87fa7cb3c',
        'officialaccounts', 'notification_messages', 'wxid_novlwrv3lqwv11',
        'gh_22b87fa7cb3c', 'wxitil', 'userexperience_alarm', 'notification_messages'];

    public function __construct(Server $server)
    {
        $this->server = $server;
        $this->getContacts();
    }

    public function getContacts()
    {
        $url = sprintf(Server::BASE_URI . '/webwxgetcontact?pass_ticket=%s&skey=%s&r=%s', $this->server->passTicket, $this->server->skey, time());

        $content = $this->server->http->json($url, [
            'BaseRequest' => $this->server->baseRequest
        ]);

        $memberList = json_decode($content, true)['MemberList'];

        $this->makeContactList($memberList);
    }

    protected function makeContactList($memberList)
    {
        foreach ($memberList as $contact) {
            if($contact['VerifyFlag'] & 8 != 0){ #公众号
                $type = 'public';
                OfficialAccount::getInstance()->push($contact);
            }elseif (in_array($contact['UserName'], static::SPECIAL_USERS)){ # 特殊账户
                $type = 'special';
                SpecialAccount::getInstance()->push($contact);
            }elseif (strstr($contact['UserName'], '@@') !== false){ # 群聊
                $type = 'group';
                GroupAccount::getInstance()->push($contact);
            }else{
                $type = 'contact';
                ContactAccount::getInstance()->push($contact);
            }
            Account::getInstance()->put($contact['UserName'], ['type' => $type, 'info' => $contact]);
        }
    }

}