<?php
// +----------------------------------------------------------------------
// | 在我们年轻的城市里，没有不可能的事！
// +----------------------------------------------------------------------
// | Copyright (c) 2019 http://robot.xiaohuoban.com All rights reserved.
// +----------------------------------------------------------------------
// | Author : Jansen <6206574@qq.com>
// +----------------------------------------------------------------------
namespace Hanson\Vbot\Support;
use Hanson\Vbot\Foundation\Vbot;
class Lang{
    private $languages;
    protected $vbot;
    public function __construct(Vbot $vbot){
        $this->vbot = $vbot;
    }
    public function get($msg){
        if (empty($this->languages)){
            $langFilePath = dirname(__DIR__).'/Languages/'.$this->vbot->config['lang'].'.php';
            if(file_exists($langFilePath)){
                $this->languages = require_once $langFilePath;
            }
        }
        return isset($this->languages[$msg])?$this->languages[$msg]:$msg;
    }
    public function setType($type){
        $this->type = $type;
    }
}