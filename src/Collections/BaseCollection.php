<?php


namespace Hanson\Vbot\Collections;


use Illuminate\Support\Collection;

class BaseCollection extends Collection
{

    /**
     * 根据昵称获取对象
     *
     * @param $nickname
     * @param bool $blur
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
     * @return mixed
     */
    public function getUsernameByRemarkName($remark, $blur = false)
    {
        return $this->getUsername($remark, 'RemarkName', $blur);
    }

    /**
     * 获取Username
     *
     * @param $search
     * @param $key
     * @param bool $blur
     * @return string
     */
    public function getUsername($search, $key, $blur = false)
    {
        return $this->search(function ($item) use ($search, $key, $blur) {

            if (!isset($item[$key])) return false;

            if ($blur && str_contains($item[$key], $search)) {
                return true;
            } elseif (!$blur && $item[$key] === $search) {
                return true;
            }

            return false;
        });
    }

    /**
     * 获取整个数组
     *
     * @param $search
     * @param $key
     * @param bool $first
     * @param bool $blur
     * @return mixed|static
     */
    public function getObject($search, $key, $first = false, $blur =false)
    {
        $objects = $this->filter(function ($item) use ($search, $key, $blur) {

            if (!isset($item[$key])) return false;

            if ($blur && str_contains($item[$key], $search)) {
                return true;
            } elseif (!$blur && $item[$key] === $search) {
                return true;
            }

            return false;
        });

        return $first ? $objects->first() : $objects;
    }


}