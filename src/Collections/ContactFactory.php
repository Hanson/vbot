<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/12
 * Time: 20:41
 */

namespace Hanson\Vbot\Collections;


use Hanson\Vbot\Support\FileManager;

class ContactFactory
{

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
        $this->makeContactList();

        $contact = contact()->get(myself()->username);
        myself()->alias = isset($contact['Alias']) ? $contact['Alias'] : myself()->nickname ? : myself()->username;

        $this->getBatchGroupMembers();

        if(server()->config['debug']){
            FileManager::download('contact.json', json_encode(contact()->all()));
            FileManager::download('member.json', json_encode(member()->all()));
            FileManager::download('group.json', json_encode(group()->all()));
            FileManager::download('official.json', json_encode(official()->all()));
            FileManager::download('special.json', json_encode(Special::getInstance()->all()));
        }
    }

    /**
     * make instance model
     *
     */
    public function makeContactList()
    {
        $url = sprintf(server()->baseUri . '/webwxgetcontact?pass_ticket=%s&skey=%s&r=%s', server()->passTicket, server()->skey, time());

        $memberList = http()->json($url, [], true)['MemberList'];

        foreach ($memberList as $contact) {
            if(official()->isOfficial($contact['VerifyFlag'])){ #公众号
                Official::getInstance()->put($contact['UserName'], $contact);
            }elseif (in_array($contact['UserName'], static::SPECIAL_USERS)){ # 特殊账户
                Special::getInstance()->put($contact['UserName'], $contact);
            }elseif (strstr($contact['UserName'], '@@') !== false){ # 群聊
                group()->put($contact['UserName'], $contact);
            }else{
                contact()->put($contact['UserName'], $contact);
            }
        }
    }

    /**
     * 获取群组成员
     */
    public function getBatchGroupMembers()
    {
        $url = sprintf(server()->baseUri . '/webwxbatchgetcontact?type=ex&r=%s&pass_ticket=%s', time(), server()->passTicket);

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
            group()->put($group['UserName'], $groupAccount);
            foreach ($group['MemberList'] as $member) {
                member()->put($member['UserName'], $member);
            }
        }

    }

}