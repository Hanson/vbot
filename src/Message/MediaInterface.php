<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 2:53
 */

namespace Hanson\Vbot\Message;


interface MediaInterface
{

    /**
     * 下载文件
     *
     * @return mixed
     */
    public function download();

}