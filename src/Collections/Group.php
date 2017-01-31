<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56
 */

namespace Hanson\Vbot\Collections;


use Hanson\Vbot\Support\Console;
use Illuminate\Support\Collection;

class Group extends Collection
{

    static $instance = null;

    /**
     * create a single instance
     *
     * @return Group
     */
    public static function getInstance()
    {
        if(static::$instance === null){
            static::$instance = new Group();
        }

        return static::$instance;
    }

    /**
     * 判断是否群组
     *
     * @param $userName
     * @return bool
     */
    public static function isGroup($userName){
        return strstr($userName, '@@') !== false;
    }

    /**
     * 根据群名筛选群组
     *
     * @param $name
     * @param bool $blur
     * @return static
     */
    public function getGroupsByNickname($name, $blur = false)
    {
        $groups = $this->filter(function($value, $key) use ($name, $blur){
           if(!$blur){
               return $value['NickName'] === $name;
           }else{
               return str_contains($value['NickName'], $name);
           }
        });

        return $groups;
    }

    /**
     * 创建群聊天
     * 
     * @param array $contacts
     * @return bool
     */
    public function create(array $contacts)
    {
        $url = sprintf('%s/webwxcreatechatroom?lang=zh_CN&r=%s', server()->baseUri, time());

        $result = http()->json($url, [
            'MemberCount' => count($contacts),
            'MemberList' => $this->makeMemberList($contacts),
            'Topic' => '',
            'BaseRequest' => server()->baseRequest
        ], true);

        if($result['BaseResponse']['Ret'] != 0){
            return false;
        }

        return $this->add($result['ChatRoomName']);
    }

    /**
     * 删除群成员
     *
     * @param $group
     * @param $members
     * @return bool
     */
    public function deleteMember($group, $members)
    {
        $members = is_string($members) ? [$members] : $members;
        $result = http()->json(sprintf('%s/webwxupdatechatroom?fun=delmember&pass_ticket=%s', server()->baseUri, server()->passTicket), [
            'BaseRequest' => server()->baseRequest,
            'ChatRoomName' => $group,
            'DelMemberList' => implode(',', $members)
        ], true);

        return $result['BaseResponse']['Ret'] == 0;
    }

    /**
     * 添加群成员
     *
     * @param $groupUsername
     * @param $members
     * @return bool
     */
    public function addMember($groupUsername, $members)
    {
        $group = group()->get($groupUsername);
        $groupCount = count($group['MemberList']);
        list($fun, $key) = $groupCount > 40 ? ['invitemember', 'InviteMemberList'] : ['addmember', 'AddMemberList'];
        $members = is_string($members) ? [$members] : $members;
        $result = http()->json(sprintf('%s/webwxupdatechatroom?fun=%s&pass_ticket=%s', server()->baseUri, $fun, server()->passTicket), [
            'BaseRequest' => server()->baseRequest,
            'ChatRoomName' => $groupUsername,
            $key => implode(',', $members)
        ], true);

        return $result['BaseResponse']['Ret'] == 0;
    }

    /**
     * 设置群名称
     *
     * @param $group
     * @param $name
     * @return bool
     */
    public function setGroupName($group, $name)
    {
        $result = http()->post(sprintf('%s/webwxupdatechatroom?fun=modtopic&pass_ticket=%s', server()->baseUri, server()->passTicket),
            json_encode([
            'BaseRequest' => server()->baseRequest,
            'ChatRoomName' => $group,
            'NewTopic' => $name
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), true);

        return $result['BaseResponse']['Ret'] == 0;
    }

    /**
     * 增加群聊天到group
     * 
     * @param $username
     * @return bool
     */
    private function add($username)
    {
        $result = http()->json(sprintf('%s/webwxbatchgetcontact?type=ex&r=%s&pass_ticket=%s', server()->baseUri, time(), server()->passTicket), [
            'Count' => 1,
            'BaseRequest' => server()->baseRequest,
            'List' => [
                [
                    'ChatRoomId' => '',
                    'UserName' => $username
                ]
            ]
        ], true);

        if($result['BaseResponse']['Ret'] != 0){
            Console::log('增加聊天群组失败 '.$username, Console::WARNING);
            return false;
        }

        group()->put($username, $result['ContactList'][0]);

        return $result['ContactList'][0];
    }

    /**
     * 生成member list 格式
     * 
     * @param $contacts
     * @return array
     */
    private function makeMemberList($contacts)
    {
        $memberList = [];

        foreach ($contacts as $contact) {
            $memberList[] = ['UserName' => $contact];
        }
        return $memberList;
    }

}