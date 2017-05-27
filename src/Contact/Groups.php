<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56.
 */

namespace Hanson\Vbot\Contact;

use Hanson\Vbot\Exceptions\CreateGroupException;

class Groups extends Contacts
{
    /**
     * 判断是否群组.
     *
     * @param $userName
     *
     * @return bool
     */
    public function isGroup($userName)
    {
        return strstr($userName, '@@') !== false;
    }

    /**
     * 根据群名筛选群组.
     *
     * @param $nickname
     * @param bool $blur
     *
     * @return static
     */
    public function getGroupsByNickname($nickname, $blur = false)
    {
        return $this->getObject($nickname, 'NickName', $blur);
    }

    /**
     * 根据username获取群成员.
     *
     * @param $username
     * @param $memberUsername
     *
     * @return mixed
     */
    public function getMemberByUsername($username, $memberUsername)
    {
        $members = $this->get($username)['MemberList'];

        if (count($members) === 0) {
            return;
        }

        foreach ($members as $member) {
            if ($memberUsername === $member['UserName']) {
                return $member;
            }
        }
    }

    /**
     * 根据昵称搜索群成员.
     *
     * @param $groupUsername
     * @param $memberNickname
     * @param bool $blur
     *
     * @return array|bool
     */
    public function getMembersByNickname($groupUsername, $memberNickname, $blur = false)
    {
        $group = $this->get($groupUsername);

        if (!$group) {
            return false;
        }

        $result = [];

        foreach ($group['MemberList'] as $member) {
            if ($blur && str_contains($member['NickName'], $memberNickname)) {
                $result[] = $member;
            } elseif (!$blur && $member['NickName'] === $memberNickname) {
                $result[] = $member;
            }
        }

        return $result;
    }

    /**
     * 创建群聊天.
     *
     * @param array $contacts
     *
     * @return bool
     */
    public function create(array $contacts)
    {
        $url = sprintf('%s/webwxcreatechatroom?lang=zh_CN&r=%s', $this->vbot->config['server.uri.base'], time());

        $result = $this->vbot->http->json($url, [
            'MemberCount' => count($contacts),
            'MemberList'  => $this->makeMemberList($contacts),
            'Topic'       => '',
            'BaseRequest' => $this->vbot->config['server.baseRequest'],
        ], true);

        if ($result['BaseResponse']['Ret'] != 0) {
            return false;
        }

        return $this->add($result['ChatRoomName']);
    }

    /**
     * 删除群成员.
     *
     * @param $group
     * @param $members
     *
     * @return bool
     */
    public function deleteMember($group, $members)
    {
        $members = is_string($members) ? [$members] : $members;
        $result = $this->vbot->http->json(sprintf('%s/webwxupdatechatroom?fun=delmember&pass_ticket=%s', $this->vbot->config['server.uri.base'], $this->vbot->config['server.passTicket']), [
            'BaseRequest'   => $this->vbot->config['server.baseRequest'],
            'ChatRoomName'  => $group,
            'DelMemberList' => implode(',', $members),
        ], true);

        return $result['BaseResponse']['Ret'] == 0;
    }

    /**
     * 添加群成员.
     *
     * @param $groupUsername
     * @param $members
     *
     * @return bool
     */
    public function addMember($groupUsername, $members)
    {
        if (!$groupUsername) {
            return false;
        }
        $group = $this->get($groupUsername);

        if (!$group) {
            return false;
        }

        $groupCount = count($group['MemberList']);
        list($fun, $key) = $groupCount > 40 ? ['invitemember', 'InviteMemberList'] : ['addmember', 'AddMemberList'];
        $members = is_string($members) ? [$members] : $members;

        $result = $this->vbot->http->json(sprintf('%s/webwxupdatechatroom?fun=%s&pass_ticket=%s', $this->vbot->config['server.uri.base'], $fun, $this->vbot->config['server.passTicket']), [
            'BaseRequest'  => $this->vbot->config['server.baseRequest'],
            'ChatRoomName' => $groupUsername,
            $key           => implode(',', $members),
        ], true);

        return $result['BaseResponse']['Ret'] == 0;
    }

    /**
     * 设置群名称.
     *
     * @param $group
     * @param $name
     *
     * @return bool
     */
    public function setGroupName($group, $name)
    {
        $result = $this->vbot->http->post(sprintf('%s/webwxupdatechatroom?fun=modtopic&pass_ticket=%s', $this->vbot->config['server.uri.base'], $this->vbot->config['server.passTicket']),
            json_encode([
            'BaseRequest'  => $this->vbot->config['server.baseRequest'],
            'ChatRoomName' => $group,
            'NewTopic'     => $name,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), true);

        return $result['BaseResponse']['Ret'] == 0;
    }

    /**
     * 增加群聊天到group.
     *
     * @param $username
     *
     * @throws CreateGroupException
     *
     * @return bool
     */
    private function add($username)
    {
        $result = $this->vbot->http->json(sprintf('%s/webwxbatchgetcontact?type=ex&r=%s&pass_ticket=%s', $this->vbot->config['server.uri.base'], time(), $this->vbot->config['server.passTicket']), [
            'Count'       => 1,
            'BaseRequest' => $this->vbot->config['server.baseRequest'],
            'List'        => [
                [
                    'ChatRoomId' => '',
                    'UserName'   => $username,
                ],
            ],
        ], true);

        if ($result['BaseResponse']['Ret'] != 0) {
            throw new CreateGroupException('create group chat fail.');
        }

        $this->put($username, $result['ContactList'][0]);

        return $result['ContactList'][0];
    }

    /**
     * 更新群组.
     *
     * @param $username
     * @param null $list
     *
     * @return array
     */
    public function update($username, $list = null) :array
    {
        $username = is_array($username) ?: [$username];

        return parent::update($username, $this->makeUsernameList($username));
    }

    /**
     * 生成username list 格式.
     *
     * @param $username
     *
     * @return array
     */
    public function makeUsernameList($username)
    {
        $usernameList = [];

        foreach ($username as $item) {
            $usernameList[] = ['UserName' => $item, 'ChatRoomId' => ''];
        }

        return $usernameList;
    }

    /**
     * 生成member list 格式.
     *
     * @param $contacts
     *
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

    /**
     * 存储群组前批量修改群成员nickname.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return \Illuminate\Support\Collection
     */
    public function put($key, $value)
    {
        foreach ($value['MemberList'] as &$member) {
            $member = $this->format($member);
        }

        return parent::put($key, $value);
    }

    /**
     * 修改群组获取，为空时更新群组.
     *
     * @param mixed $key
     * @param null  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $group = parent::get($key);

        return $group ?: current($this->update($key));
    }
}
