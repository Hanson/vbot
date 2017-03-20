<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56
 */

namespace Hanson\Vbot\Collections;


class Contact extends BaseCollection
{

    /**
     * @var Contact
     */
    static $instance = null;

    /**
     * create a single instance
     *
     * @return Contact
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new Contact();
        }

        return static::$instance;
    }

    /**
     * 根据微信号获取联系人
     * @deprecated
     * @param $alias
     * @return mixed
     */
    public function getContactById($alias)
    {
        return $this->getContactById($alias);
    }

    /**
     * 根据微信号获取联系人
     *
     * @param $alias
     * @return mixed
     */
    public function getContactByAlias($alias)
    {
        return $this->getObject($alias, 'Alias', true);
    }

    /**
     * 根据微信号获取联系username
     * @deprecated
     * @param $alias
     * @return mixed
     */
    public function getUsernameById($alias)
    {
        return $this->getUsernameByAlias($alias);
    }

    /**
     * 根据微信号获取联系username
     *
     * @param $alias
     * @return mixed
     */
    public function getUsernameByAlias($alias)
    {
        return $this->getUsername($alias, 'Alias');
    }

    /**
     * 设置备注
     *
     * @param $username
     * @param $remarkName
     * @return bool
     */
    public function setRemarkName($username, $remarkName)
    {
        $url = sprintf('%s/webwxoplog?lang=zh_CN&pass_ticket=%s', server()->baseUri, server()->passTicket);

        $result = http()->post($url, json_encode([
            'UserName' => $username,
            'CmdId' => 2,
            'RemarkName' => $remarkName,
            'BaseRequest' => server()->baseRequest
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), true);

        return $result['BaseResponse']['Ret'] == 0;
    }

    /**
     * 设置是否置顶
     *
     * @param $username
     * @param bool $isStick
     * @return bool
     */
    public function setStick($username, $isStick = true)
    {
        $url = sprintf('%s/webwxoplog?lang=zh_CN&pass_ticket=%s', server()->baseUri, server()->passTicket);

        $result = http()->json($url, [
            'UserName' => $username,
            'CmdId' => 3,
            'OP' => (int)$isStick,
            'BaseRequest' => server()->baseRequest
        ], true);

        return $result['BaseResponse']['Ret'] == 0;
    }

}