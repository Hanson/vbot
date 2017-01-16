<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2017/1/13
 * Time: 17:23
 */

namespace Hanson\Vbot\Support;


trait ObjectAble
{

    public function toObject(Array $array)
    {
        return json_decode(json_encode($array));
    }

}