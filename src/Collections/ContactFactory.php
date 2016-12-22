<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/12
 * Time: 20:41
 */

namespace Hanson\Robot\Collections;


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

    /**
     * make instance model
     *
     * @param $memberList
     */
    protected function makeContactList($memberList)
    {
        foreach ($memberList as $contact) {
            if($contact['VerifyFlag'] & 8 != 0){ #公众号
                $type = 'public';
                OfficialAccount::getInstance()->put($contact['UserName'], $contact);
            }elseif (in_array($contact['UserName'], static::SPECIAL_USERS)){ # 特殊账户
                $type = 'special';
                SpecialAccount::getInstance()->put($contact['UserName'], $contact);
            }elseif (strstr($contact['UserName'], '@@') !== false){ # 群聊
                $type = 'group';
                GroupAccount::getInstance()->put($contact['UserName'], $contact);
            }else{
                $type = 'contact';
                ContactAccount::getInstance()->put($contact['UserName'], $contact);
            }
            Account::getInstance()->addNormalMember([$contact['UserName'] => ['type' => $type, 'info' => $contact]]);
        }

        $this->getBatchGroupMembers();
        file_put_contents($this->server->config['tmp'] . 'account.json', json_encode(Account::getInstance()->all()));
    }

    /**
     * get group members by api
     */
    public function getBatchGroupMembers()
    {
        $url = sprintf(Server::BASE_URI . '/webwxbatchgetcontact?type=ex&r=%s&pass_ticket=%s', time(), $this->server->passTicket);

        $list = [];
        GroupAccount::getInstance()->each(function($item, $key) use (&$list){
            $list[] = ['UserName' => $key, 'EncryChatRoomId' => ''];
        });

        file_put_contents($this->server->config['tmp'] . 'debug.json', json_encode([
            'BaseRequest' => $this->server->baseRequest,
            'Count' => GroupAccount::getInstance()->count(),
            'List' => $list
        ]));

        $content = $this->server->http->json($url, [
            'BaseRequest' => $this->server->baseRequest,
            'Count' => GroupAccount::getInstance()->count(),
            'List' => $list
        ], true);

        $this->initGroupMembers($content);
    }

    /**
     * init group members and chat room id
     *
     * @param $array
     */
    private function initGroupMembers($array)
    {
        foreach ($array['ContactList'] as $group) {
            $groupAccount =  GroupAccount::getInstance()->get($group['UserName']);
            $groupAccount['MemberList'] = $group['MemberList'];
            $groupAccount['ChatRoomId'] = $group['EncryChatRoomId'];
            GroupAccount::getInstance()->put($group['UserName'], $groupAccount);
            foreach ($group['MemberList'] as $member) {
                Account::getInstance()->addGroupMember([$member['UserName'] => ['type' => 'groupMember', 'info' => $member, 'group' => $group]]);
            }
        }

    }

}