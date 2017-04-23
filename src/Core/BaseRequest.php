<?php


namespace Hanson\Vbot\Core;


use Hanson\Vbot\Foundation\Vbot;

class BaseRequest
{

    public $uin;

    public $sid;

    public $skey;

    public $deviceId;

    /**
     * @var Vbot
     */
    protected $vbot;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    public function init()
    {
        $this->uin = $this->vbot->config['server.uin'];
        $this->sid = $this->vbot->config['server.sid'];
        $this->skey = $this->vbot->config['server.skey'];
        $this->deviceId = $this->vbot->config['server.deviceId'];

        $this->vbot->config['server.baseRequest'] = $this->toArray();
    }

    public function toArray()
    {
        return [
            'Uin' => intval($this->uin),
            'Sid' => $this->sid,
            'Skey' => $this->skey,
            'DeviceID' => $this->deviceId,
        ];
    }

}