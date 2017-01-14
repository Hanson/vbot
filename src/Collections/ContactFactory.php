<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/12
 * Time: 20:41
 */

namespace Hanson\Robot\Collections;


use Hanson\Robot\Core\Server;
use Hanson\Robot\Support\Console;
use Hanson\Robot\Support\ObjectAble;

class ContactFactory
{
    use ObjectAble;

    const SPECIAL_USERS = ['newsapp', 'fmessage', 'filehelper', 'weibo', 'qqmail',
        'fmessage', 'tmessage', 'qmessage', 'qqsync', 'floatbottle',
        'lbsapp', 'shakeapp', 'medianote', 'qqfriend', 'readerapp',
        'blogapp', 'facebookapp', 'masssendapp', 'meishiapp',
        'feedsapp', 'voip', 'blogappweixin', 'weixin', 'brandsessionholder',
        'weixinreminder', 'wxid_novlwrv3lqwv11', 'gh_22b87fa7cb3c',
        'officialaccounts', 'notification_messages', 'wxid_novlwrv3lqwv11',
        'gh_22b87fa7cb3c', 'wxitil', 'userexperience_alarm', 'notification_messages'];

    public function __construct()
    {
        $this->getContacts();
    }

    public function getContacts()
    {
        $url = sprintf(Server::BASE_URI . '/webwxgetcontact?pass_ticket=%s&skey=%s&r=%s', server()->passTicket, server()->skey, time());

        $content = http()->json($url, [
            'BaseRequest' => server()->baseRequest
        ], true);

        $this->makeContactList($content['MemberList']);
    }

    /**
     * make instance model
     *
     * @param $memberList
     */
    protected function makeContactList($memberList)
    {
        foreach ($memberList as $contact) {
            if(($contact['VerifyFlag'] & 8) != 0){ #公众号
                OfficialAccount::getInstance()->put($contact['UserName'], $this->toObject($contact));
            }elseif (in_array($contact['UserName'], static::SPECIAL_USERS)){ # 特殊账户
                SpecialAccount::getInstance()->put($contact['UserName'], $this->toObject($contact));
            }elseif (strstr($contact['UserName'], '@@') !== false){ # 群聊
                group()->put($contact['UserName'], $this->toObject($contact));
            }else{
                contact()->put($contact['UserName'], $this->toObject($contact));
            }
        }

        $this->getBatchGroupMembers();
        if(server()->config['debug']){
            file_put_contents(server()->config['tmp'] . 'contact.json', json_encode(contact()->all()));
            file_put_contents(server()->config['tmp'] . 'member.json', json_encode(member()->all()));
            file_put_contents(server()->config['tmp'] . 'group.json', json_encode(group()->all()));
            file_put_contents(server()->config['tmp'] . 'OfficialAccount.json', json_encode(OfficialAccount::getInstance()->all()));
            file_put_contents(server()->config['tmp'] . 'SpecialAccount.json', json_encode(SpecialAccount::getInstance()->all()));
        }
    }

    /**
     * 获取群组成员
     */
    public function getBatchGroupMembers()
    {
        $url = sprintf(Server::BASE_URI . '/webwxbatchgetcontact?type=ex&r=%s&pass_ticket=%s', time(), server()->passTicket);

        $list = [];
        group()->each(function($item, $key) use (&$list){
            $list[] = ['UserName' => $key, 'EncryChatRoomId' => ''];
        });

        $content = http()->json($url, [
            'BaseRequest' => server()->baseRequest,
            'Count' => group()->count(),
            'List' => $list
        ], true);

        $this->initGroupMembers($content);
    }

    /**
     * 初始化群组成员
     *
     * @param $array
     */
    private function initGroupMembers($array)
    {
        foreach ($array['ContactList'] as $group) {
            $groupAccount =  group()->get($group['UserName']);
            $groupAccount['MemberList'] = $group['MemberList'];
            $groupAccount['ChatRoomId'] = $group['EncryChatRoomId'];
            group()->put($group['UserName'], $this->toObject($groupAccount));
            foreach ($group['MemberList'] as $member) {
                member()->put($member['UserName'], $this->toObject($member));
            }
        }

    }

}