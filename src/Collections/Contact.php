<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/13
 * Time: 20:56
 */

namespace Hanson\Vbot\Collections;


use Illuminate\Support\Collection;

class Contact extends Collection
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
        if(static::$instance === null){
            static::$instance = new Contact();
        }

        return static::$instance;
    }

    /**
     * 根据微信号获取联系人
     *
     * @param $id
     * @return mixed
     */
    public function getContactById($id)
    {
        return $this->filter(function($item, $key) use ($id){
            if($item['Alias'] === $id){
                return true;
            }
        })->first();
    }

    /**
     * 根据微信号获取联系username
     *
     * @param $id
     * @return mixed
     */
    public function getUsernameById($id)
    {
        return $this->search(function($item, $key) use ($id){
            if($item['Alias'] === $id){
                return true;
            }
        });
    }

}