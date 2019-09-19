<?php

namespace Hanson\Vbot\Support;

/**
 * Common 公共处理类.
 *
 * Class Common
 */
class Common
{
    /**
     * 是否json格式数据.
     *
     * @param $string json字符串
     *
     * @return bool
     */
    public static function isJson($string)
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }

    /**
     * 获取精确到毫秒的时间戳(13位).
     *
     * @return int
     */
    public static function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());

        return (float) sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

    /**
     * 在vbot中找到微信群，并返回唯一 username.
     *
     * @param object $groupObj 微信群信息
     *
     * @return array
     */
    public static function findWechatGroup($groupObj)
    {
        $result = [
            'found'    => false,
            'username' => '',
            'message'  => '',
        ];

        $nickName = $groupObj->nick_name;
        $PYInitial = $groupObj->py_initial;
        $firstMemberAttr = $groupObj->first_member_attr;
        $memberAttrs = $groupObj->member_attrs;

        $matchGroup = [];
        $groups = vbot('groups')->all();
        foreach ($groups as $group) {
            $nickNameMatch = false;
            $PYInitialMatch = false;
            $firstMemberAttrMatch = false;
            $memberAttrsMatch = true;

            //群昵称匹配
            if (str_contains($group['NickName'], $nickName)) {
                $nickNameMatch = true;
            }

            //拼音标识匹配
            if ('' != $PYInitial) {
                if (str_contains($group['PYInitial'], $PYInitial)) {
                    $PYInitialMatch = true;
                }
            } else {
                $PYInitialMatch = true;
            }

            /**
             * !! BUG妥协
             * 有时候 $group 信息中，群成员列表为空，导致匹配群员时，$group['MemberList'][0]['AttrStatus'] 报错
             * 暂代码妥协，如果 $group['MemberList'] 成员列表为空，则不检查首位成员和指定成员，直接返回 true
             * 如果由于不检查成员导致的群名重复，在最后会由于匹配多于一条记录而报错，守住底线，保证程序不出错.
             */
            if (count($group['MemberList']) > 0) {

                //首位群员标识不匹配
                if ('' != $firstMemberAttr) {
                    if ($firstMemberAttr == $group['MemberList'][0]['AttrStatus']) {
                        $firstMemberAttrMatch = true;
                    }
                } else {
                    $firstMemberAttrMatch = true;
                }

                //没包含指定群员Attr
                foreach ($memberAttrs as $attr) {
                    if (!self::isGroupMember($group['MemberList'], $attr)) {
                        $memberAttrsMatch = false;
                    }
                }
            } else {
                $firstMemberAttrMatch = true;
                $memberAttrsMatch = true;
            }

            //所有
            if ($nickNameMatch &&
                $PYInitialMatch &&
                $firstMemberAttrMatch &&
                $memberAttrsMatch) {
                $matchGroup[] = $group['UserName'];
            }
        }

        if (count($matchGroup) > 1) {
            //如果条件匹配出多个群，抛出异常
            $result['message'] = '[ERROR] not allow match more than one group';
        }

        $username = current($matchGroup);
        if (false !== $username) {
            $result['found'] = true;
            $result['username'] = $username;
        } else {
            $result['message'] = '[ERROR] wechat group not found.';
        }

        return $result;
    }

    /**
     * 在vbot中找到微信好友，并返回唯一 username.
     *
     * @param object $memberObj 会员对象
     *
     * @return array
     */
    public static function findWechatFriend($memberObj)
    {
        $result = [
            'found'    => false,
            'username' => '',
            'message'  => '',
        ];

        $nickName = $memberObj->nick_name;
        $PYInitial = $memberObj->py_initial;
        $memberAttr = $memberObj->first_member_attr;

        $matchFriend = [];
        $friends = vbot('friends')->all();
        foreach ($friends as $friend) {
            $nickNameMatch = false;
            $PYInitialMatch = false;
            $memberAttrMatch = false;

            //昵称匹配
            if (str_contains($friend['NickName'], $nickName)) {
                $nickNameMatch = true;
            }

            //拼音标识匹配
            if ('' != $PYInitial) {
                if (str_contains($friend['PYInitial'], $PYInitial)) {
                    $PYInitialMatch = true;
                }
            } else {
                $PYInitialMatch = true;
            }

            //Attr匹配
            if ($memberAttr == $friend['AttrStatus']) {
                $memberAttrMatch = true;
            }

            //所有
            if ($nickNameMatch &&
                $PYInitialMatch &&
                $memberAttrMatch) {
                $matchFriend[] = $friend['UserName'];
            }
        }

        if (count($matchFriend) > 1) {
            //如果条件匹配出多个好友，抛出异常
            $result['message'] = '[ERROR] not allow match more than one friend.';
        }

        $username = current($matchFriend);
        if (false !== $username) {
            $result['found'] = true;
            $result['username'] = $username;
        } else {
            $result['message'] = '[ERROR] wechat friend not found.';
        }

        return $result;
    }

    /**
     * 是否群员.
     *
     * @param $memberList 群员列表
     * @param $memberAttr 群员标识
     *
     * @return bool
     */
    private static function isGroupMember($memberList, $memberAttr)
    {
        foreach ($memberList as $member) {
            if ($member['AttrStatus'] == $memberAttr) {
                return true;
            }
        }

        return false;
    }

    /**
     * 下载联系人信息到文件.
     *
     * @param $contactType 联系人类型 groups/friends
     */
    public static function dumpContacts($contactType)
    {
        $uin = vbot('myself')->uin;
        $time = date('YmdHis', time());
        file_put_contents("{$contactType}_{$uin}_{$time}.txt", print_r(vbot($contactType)->all(), true));
        vbot('console')->log('dump file success.');

        return [
            'successful' => true,
            'message'    => '成功',
        ];
    }

    /**
     * 判断指定采集群是否开启.
     *
     * @param int $sourceId 采集群Id，在JD-Controller中设定
     *
     * @return bool
     */
    public static function isActivitySource($sourceId)
    {
        $distribute = vbot('config')['distribute'];

        return isset($distribute[$sourceId]);
    }

    /**
     * 判断指定目标是否开启.
     *
     * @param int $targetId 目标Id，在JD-Controller中设定
     *
     * @return bool
     */
    public static function isActivityTarget($targetId)
    {
        $distribute = vbot('config')['distribute'];

        foreach ($distribute as $sid => $source) {
            if (isset($source->targets[$targetId])) {
                return true;
            }

            if (isset($source->apis[$targetId])) {
                return true;
            }
        }

        return false;
    }
}
