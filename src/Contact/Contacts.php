<?php

namespace Hanson\Vbot\Contact;

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Support\Content;
use Illuminate\Support\Collection;

class Contacts extends Collection
{
    /**
     * @var Vbot
     */
    protected $vbot;

    public function __construct()
    {
        parent::__construct();
    }

    public function setVbot(Vbot $vbot)
    {
        $this->vbot = $vbot;

        return $this;
    }

    /**
     * 根据昵称获取对象
     *
     * @param $nickname
     * @param bool $blur
     *
     * @return bool|string
     */
    public function getUsernameByNickname($nickname, $blur = false)
    {
        return $this->getUsername($nickname, 'NickName', $blur);
    }

    /**
     * 根据备注获取对象
     *
     * @param $remark
     * @param $blur
     *
     * @return mixed
     */
    public function getUsernameByRemarkName($remark, $blur = false)
    {
        return $this->getUsername($remark, 'RemarkName', $blur);
    }

    /**
     * 获取Username.
     *
     * @param $search
     * @param $key
     * @param bool $blur
     *
     * @return string
     */
    public function getUsername($search, $key, $blur = false)
    {
        return $this->search(function ($item) use ($search, $key, $blur) {
            if (!isset($item[$key])) {
                return false;
            }

            if ($blur && str_contains($item[$key], $search)) {
                return true;
            } elseif (!$blur && $item[$key] === $search) {
                return true;
            }

            return false;
        });
    }

    /**
     * 获取整个数组.
     *
     * @param $search
     * @param $key
     * @param bool $blur
     *
     * @return mixed|static
     */
    public function getObject($search, $key, $blur = false)
    {
        $username = $this->getUsername($search, $key, $blur);

        return $username ? $this->get($username) : null;
    }

    /**
     * 根据username获取账号.
     *
     * @param $username
     *
     * @return mixed
     */
    public function getAccount($username)
    {
        if (starts_with($username, '@@')) {
            return $this->vbot->groups->get($username);
        } else {
            $account = $this->vbot->friends->get($username, null);

            $account = $account ?: $this->vbot->members->get($username, null);

            $account = $account ?: $this->vbot->officials->get($username, null);

            return $account ?: $this->vbot->specials->get($username, null);
        }
    }

    public function getAvatar($username)
    {
        $params = [
            'userName' => $username,
            'type'     => 'big',
        ];

        $api = $this->vbot->groups->isGroup($username) ? '/webwxgetheadimg' : '/webwxgeticon';

        return $this->vbot->http->get($this->vbot->config['server.uri.base'].$api, ['query' => $params]);
    }

    /**
     * 存储时处理emoji.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return Collection
     */
    public function put($key, $value)
    {
        $value = $this->format($value);

        return parent::put($key, $value);
    }

    /**
     * 处理联系人.
     *
     * @param $contact
     *
     * @return mixed
     */
    public function format($contact)
    {
        if (isset($contact['DisplayName'])) {
            $contact['DisplayName'] = Content::emojiHandle($contact['DisplayName']);
        }

        if (isset($contact['RemarkName'])) {
            $contact['RemarkName'] = Content::emojiHandle($contact['RemarkName']);
        }

        if (isset($contact['Signature'])) {
            $contact['Signature'] = Content::emojiHandle($contact['Signature']);
        }

        $contact['NickName'] = Content::emojiHandle($contact['NickName']);

        return $contact;
    }

    /**
     * 通过接口更新群组信息.
     *
     * @param $username
     * @param $list
     *
     * @return array
     */
    public function update($username, $list) :array
    {
        $usernames = is_string($username) ? [$username] : $username;

        $url = $this->vbot->config['server.uri.base'].'/webwxbatchgetcontact?type=ex&r='.time();

        $data = [
            'BaseRequest' => $this->vbot->config['server.baseRequest'],
            'Count'       => count($usernames),
            'List'        => $list,
        ];

        $response = $this->vbot->http->json($url, $data, true);

        if (!$response) {
            return [];
        }

        foreach ($response['ContactList'] as $item) {
            $this->put($item['UserName'], $item);
        }

        return is_string($username) ? head($response['ContactList']) : $response['ContactList'];
    }
}
