<?php

namespace Hanson\Vbot\Core;

use Hanson\Vbot\Foundation\Vbot;

class ContactFactory
{
    const SPECIAL_USERS = ['newsapp', 'fmessage', 'filehelper', 'weibo', 'qqmail',
        'fmessage', 'tmessage', 'qmessage', 'qqsync', 'floatbottle',
        'lbsapp', 'shakeapp', 'medianote', 'qqfriend', 'readerapp',
        'blogapp', 'facebookapp', 'masssendapp', 'meishiapp',
        'feedsapp', 'voip', 'blogappweixin', 'weixin', 'brandsessionholder',
        'weixinreminder', 'wxid_novlwrv3lqwv11', 'gh_22b87fa7cb3c',
        'officialaccounts', 'notification_messages', 'wxid_novlwrv3lqwv11',
        'gh_22b87fa7cb3c', 'wxitil', 'userexperience_alarm', 'notification_messages', ];

    /**
     * @var Vbot
     */
    private $vbot;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    public function fetchAll()
    {
        $this->fetchAllContacts();

        $this->fetchGroupMembers();

        $this->vbot->fetchContactObserver->trigger([
            'friends'   => $this->vbot->friends,
            'groups'    => $this->vbot->groups,
            'officials' => $this->vbot->officials,
            'special'   => $this->vbot->specials,
            'members'   => $this->vbot->members,
        ]);
    }

    /**
     * fetch all contacts through api.
     *
     * @param $seq
     */
    public function fetchAllContacts($seq = 0)
    {
        $url = sprintf($this->vbot->config['server.uri.base'].'/webwxgetcontact?pass_ticket=%s&skey=%s&r=%s&seq=%s',
            $this->vbot->config['server.passTicket'],
            $this->vbot->config['server.skey'],
            time(),
            $seq
        );

        $result = $this->vbot->http->json($url, [], true, ['timeout' => 60]);

        if (isset($result['MemberList']) && $result['MemberList']) {
            $this->store($result['MemberList']);
        }

        if (isset($result['Seq']) && $result['Seq'] != 0) {
            $this->fetchAllContacts($result['Seq']);
        }
    }

    /**
     * create and save contacts to collections.
     *
     * @param $memberList
     */
    public function store($memberList)
    {
        foreach ($memberList as $contact) {
            if (in_array($contact['UserName'], static::SPECIAL_USERS)) {
                $this->vbot->specials->put($contact['UserName'], $contact);
            } elseif ($this->vbot->officials->isOfficial($contact['VerifyFlag'])) {
                $this->vbot->officials->put($contact['UserName'], $contact);
            } elseif (strstr($contact['UserName'], '@@') !== false) {
                $this->vbot->groups->put($contact['UserName'], $contact);
            } else {
                $this->vbot->friends->put($contact['UserName'], $contact);
            }
        }
    }

    /**
     * fetch group members.
     */
    public function fetchGroupMembers()
    {
        $url = sprintf($this->vbot->config['server.uri.base'].'/webwxbatchgetcontact?type=ex&r=%s&pass_ticket=%s',
            time(), $this->vbot->config['server.passTicket']
        );

        $list = [];
        $this->vbot->groups->each(function ($item, $key) use (&$list) {
            $list[] = ['UserName' => $key, 'EncryChatRoomId' => ''];
        });

        $content = $this->vbot->http->json($url, [
            'BaseRequest' => $this->vbot->config['server.baseRequest'],
            'Count'       => $this->vbot->groups->count(),
            'List'        => $list,
        ], true, ['timeout' => 60]);

        $this->storeMembers($content);
    }

    /**
     * store group members.
     *
     * @param $array
     */
    private function storeMembers($array)
    {
        if (isset($array['ContactList']) && $array['ContactList']) {
            foreach ($array['ContactList'] as $group) {
                $groupAccount = $this->vbot->groups->get($group['UserName']);
                $groupAccount['MemberList'] = $group['MemberList'];
                $groupAccount['ChatRoomId'] = $group['EncryChatRoomId'];
                $this->vbot->groups->put($group['UserName'], $groupAccount);
                foreach ($group['MemberList'] as $member) {
                    $this->vbot->members->put($member['UserName'], $member);
                }
            }
        }
    }
}
