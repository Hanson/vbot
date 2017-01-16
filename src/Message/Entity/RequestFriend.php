<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2017/1/15
 * Time: 12:29
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MessageInterface;

class RequestFriend extends Message implements MessageInterface
{

    /**
     * @var array 信息
     */
    public $info;

    public $avatar;

    /**
     * 国内为省，国外为国
     *
     * @var string
     */
    public $province;

    /**
     * 城市
     *
     * @var string
     */
    public $city;

    const ADD = 2;
    const VIA = 3;

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    public function make()
    {
        $this->info = $this->msg['RecommendInfo'];
        $this->parseContent();
    }

    private function parseContent()
    {
        $isMatch = preg_match('/province="(.+?)"\scity="(.+?)".+bigheadimgurl="(.+?)"/', $this->msg['Content'], $matches);

        if($isMatch){
            $this->province = $matches[1];
            $this->city = $matches[2];
            $this->avatar = $matches[3];
        }
    }

    /**
     * 验证通过好友
     *
     * @param $code
     * @param null $ticket
     * @return bool
     */
    public function verifyUser($code, $ticket = null)
    {
        $url = sprintf(server()->baseUri.'/webwxverifyuser?lang=zh_CN&r=%s&pass_ticket=%s' ,time()*1000, server()->passTicket);
        $data = [
            'BaseRequest' => server()->baseRequest,
            'Opcode' => $code,
            'VerifyUserListSize' => 1,
            'VerifyUserList' => [$ticket ? : $this->verifyTicket()],
            'VerifyContent' => '',
            'SceneListCount' => 1,
            'SceneList' => [33],
            'skey' => server()->skey
        ];

        $result = http()->json($url, $data, true);

        return $result['BaseResponse']['Ret'] == 0;
    }

    /**
     * 返回通过好友申请所需的数组
     *
     * @return array
     */
    public function verifyTicket()
    {
        return [
            'Value' => $this->info['UserName'],
            'VerifyUserTicket' => $this->info['Ticket']
        ];
    }
}