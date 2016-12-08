<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2016/12/7
 * Time: 16:12
 */

namespace Hanson\Robot;


use Endroid\QrCode\QrCode;
use GuzzleHttp\Client;
use Masterminds\HTML5\Exception;
use Symfony\Component\DomCrawler\Crawler;

class Robot
{

    private $client;

    public $tmpPath;

    public $debug;

    private $uuid;

    private $baseUri = 'https://wx2.qq.com/cgi-bin/mmwebwx-bin';

    private $baseHost = 'wx2.qq.com';

    private $redirectUri;

    private $skey;

    private $sid;

    private $uin;

    private $passTicket;

    private $deviceId;

    private $baseRequest;

    private $syncKey;

    private $myAccount;

    private $syncKeyStr;

    private $memberList;

    private $contactList;

    private $publicList;

    private $specialList;

    private $groupList;

    private $accountInfo = [
        'groupMember' => [],
        'normalMember' => []
    ];

    private $groupMembers;

    private $encryChatRoomId;

    private $syncHost;

    public function __construct(Array $option = [])
    {
        $this->client = new Client([
            'cookies' => true
        ]);

        $this->handleOption($option);
    }

    /**
     * 处理option
     *
     * @param $option
     */
    private function handleOption($option)
    {
        $this->tmpPath = $option['tmp'] ?? sys_get_temp_dir();
        $this->debug = $option['debug'] ?? true;
    }

    public function run()
    {
        $this->getUuid();

        $this->generateQrCode();

        $this->log('[INFO] 请用微信扫码');

        if($this->waitForLogin() !== 200){
            die('[ERROR] 微信登录失败');
        }

        if(!$this->login()){
            die('[ERROR] 登录失败');
        }

        $this->log('[INFO] 登录成功');

        if(!$this->init()){
            die('[INFO] 微信初始化失败');
        }

        $this->log('[INFO] 微信初始化成功');

        $this->statusNotify();
        $this->getContact();

        $this->log('[INFO] 获取 ' . count($this->contactList) . ' 个联系人');
        $this->log('[INFO] 开始获取信息');
    }

    /**
     * 获取UUID
     * @throws \Exception
     */
    public function getUuid()
    {
        $content = $this->client->get('https://login.weixin.qq.com/jslogin', [
            'query' => [
                'appid' => 'wx782c26e4c19acffb',
                'fun' => 'new',
                'lang' => 'zh_CN',
                '_' => time() * 1000 . random_int(1, 999)
            ]
        ])->getBody()->getContents();

        preg_match('/window.QRLogin.code = (\d+); window.QRLogin.uuid = \"(\S+?)\"/', $content, $matches);

        if(!$matches){
            throw new \Exception('获取UUID失败');
        }

        $this->uuid = $matches[2];
    }

    /**
     * 生成登录二维码
     */
    public function generateQrCode()
    {
        $url = 'https://login.weixin.qq.com/l/' . $this->uuid;

        $qrCode = new QrCode($url);

        $file = $this->tmpPath . 'login_qr_code.png';

        $qrCode->save($file);

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            system($file);
        }
    }

    /**
     * 等待登录
     *
     * @return int
     */
    public function waitForLogin(): int
    {
        $urlTemplate = 'https://login.weixin.qq.com/cgi-bin/mmwebwx-bin/login?tip=%s&uuid=%s&_=%s';

        $retryTime = 10;
        $tip = 1;
        $code = 0;

        while($retryTime > 0){
            $url = sprintf($urlTemplate, $tip, $this->uuid, time());

            $content = $this->client->get($url)->getBody()->getContents();

            preg_match('/window.code=(\d+);/', $content, $matches);

            $code = $matches[1];
            switch($code){
                case '201':
                    $this->log('[INFO] 请点击确认进行登录');
                    $tip = 0;
                    break;
                case '200':
                    preg_match('/window.redirect_uri="(\S+?)";/', $content, $matches);
                    $this->redirectUri = $matches[1] . '&fun=new';
                    return $code;
                case '408':
                    $this->log('[ERROR] 微信登录超时。将在 1 秒后重试');
                    $tip = 1;
                    $retryTime -= 1;
                    sleep(1);
                    break;
                default:
                    $this->log("[ERROR] 微信登录异常。异常码：$code 。1 秒后重试");
                    $tip = 1;
                    $retryTime -= 1;
                    sleep(1);
                    break;
            }
        }
        return $code;
    }

    /**
     * 登录
     *
     * @return bool
     */
    public function login()
    {
        $content = $this->client->get($this->redirectUri)->getBody()->getContents();

        $crawler = new Crawler($content);
        $this->skey = $crawler->filter('error skey')->text();
        $this->sid = $crawler->filter('error wxsid')->text();
        $this->uin = $crawler->filter('error wxuin')->text();
        $this->passTicket = $crawler->filter('error pass_ticket')->text();

        if(in_array('', [$this->skey, $this->sid, $this->uin, $this->passTicket])){
            return false;
        }

        $this->deviceId = 'e' . strval(random_int(100000000000000, 999999999999999));

        $this->baseRequest = [
            'Uin' => $this->uin,
            'Sid' => $this->sid,
            'Skey' => $this->skey,
            'DeviceID' => $this->deviceId
        ];

        return true;
    }

    /**
     * 初始化
     *
     * @return bool
     */
    public function init()
    {
        $url = sprintf($this->baseUri . '/webwxinit?r=%i&lang=en_US&pass_ticket=%s', time(), $this->passTicket);

        $content = $this->client->post($url, [
            'json' => [
                'BaseRequest' => $this->baseRequest
            ]
        ])->getBody()->getContents();

        $result = json_decode($content, true);

        $this->generateSyncKey($result);

        $this->myAccount = $result['User'];

        return $result['BaseResponse']['Ret'] == 0;
    }

    public function generateSyncKey($result)
    {
        $this->syncKey = $result['SyncKey'];

        $syncKey = [];

        foreach ($this->syncKey['List'] as $item) {
            $syncKey[] = $item['Key'] . '_' . $item['Val'];
        }

        $this->syncKeyStr = implode('|', $syncKey);
    }

    public function statusNotify()
    {
        $url = sprintf($this->baseUri . '/webwxstatusnotify?lang=zh_CN&pass_ticket=%s', $this->passTicket);

        $content = $this->client->post($url, [
            'json' => [
                'BaseRequest' => $this->baseRequest,
                'Code' => 3,
                'FromUserName' => $this->myAccount['UserName'],
                'ToUserName' => $this->myAccount['UserName'],
                'ClientMsgId' => time()
            ]
        ])->getBody()->getContents();

        $this->debug($content);

        return json_decode($content)->BaseResponse->Ret == 0;
    }

    public function getContact()
    {
        $url = sprintf($this->baseUri . '/webwxgetcontact?pass_ticket=%s&skey=%s&r=%s', $this->passTicket, $this->skey, time());

        $content = $this->client->post($url, ['json' => []])->getBody()->getContents();

        if($this->debug){
            file_put_contents($this->tmpPath . 'contacts.json', $content);
        }

        $result = json_decode($content, true);

        $this->memberList = $result['MemberList'];

        $specialUsers = ['newsapp', 'fmessage', 'filehelper', 'weibo', 'qqmail',
            'fmessage', 'tmessage', 'qmessage', 'qqsync', 'floatbottle',
            'lbsapp', 'shakeapp', 'medianote', 'qqfriend', 'readerapp',
            'blogapp', 'facebookapp', 'masssendapp', 'meishiapp',
            'feedsapp', 'voip', 'blogappweixin', 'weixin', 'brandsessionholder',
            'weixinreminder', 'wxid_novlwrv3lqwv11', 'gh_22b87fa7cb3c',
            'officialaccounts', 'notification_messages', 'wxid_novlwrv3lqwv11',
            'gh_22b87fa7cb3c', 'wxitil', 'userexperience_alarm', 'notification_messages'];

        foreach ($this->memberList as $contact) {
            if($contact['VerifyFlag'] & 8 != 0){ #公众号
                $this->publicList[] = $contact;
                $this->accountInfo['normalMember'][$contact['UserName']] = ['type' => 'public', 'info' => $contact];
            }elseif (in_array($contact['UserName'], $specialUsers)){ # 特殊账户
                $this->specialList[] = $contact;
                $this->accountInfo['normalMember'][$contact['UserName']] = ['type' => 'special', 'info' => $contact];
            }elseif (strstr($contact['UserName'], '@@') !== false){ # 群聊
                $this->groupList[] = $contact;
                $this->accountInfo['normalMember'][$contact['UserName']] = ['type' => 'group', 'info' => $contact];
            }elseif ($contact['UserName'] === $this->myAccount['UserName']){ # 自己
                $this->accountInfo['normalMember'][$contact['UserName']] = ['type' => 'self', 'info' => $contact];
            }else{
                $this->contactList[] = $contact;
                $this->accountInfo['normalMember'][$contact['UserName']] = ['type' => 'contact', 'info' => $contact];
            }
        }

        $this->getBatchGroupMembers();

        foreach ($this->groupMembers as $key => $group) {
            foreach ($group as $member) {
                if(!in_array($member['UserName'], $this->accountInfo)){
                    $this->accountInfo['groupMember'][$member['UserName']] = ['type' => 'group_member', 'info' => $member, 'group' => $group];
                }
            }
        }

        if($this->debug){
            file_put_contents($this->tmpPath . 'contact_list.json', json_encode($this->contactList));
            file_put_contents($this->tmpPath . 'special_list.json', json_encode($this->specialList));
            file_put_contents($this->tmpPath . 'group_list.json', json_encode($this->groupList));
            file_put_contents($this->tmpPath . 'public_list.json', json_encode($this->publicList));
            file_put_contents($this->tmpPath . 'member_list.json', json_encode($this->memberList));
            file_put_contents($this->tmpPath . 'group_users.json', json_encode($this->groupMembers));
            file_put_contents($this->tmpPath . 'account_info.json', json_encode($this->accountInfo));
        }

        return true;
    }

    public function getBatchGroupMembers()
    {
        $url = sprintf($this->baseUri . '/webwxbatchgetcontact?type=ex&r=%s&pass_ticket=%s', time(), $this->passTicket);

        $list = [];
        foreach ($this->groupList as $group) {
            $list[] = ['UserName' => $group['UserName'], 'EncryChatRoomId' => ''];
        }

        $content = $this->client->post($url, [
            'json' => [
                'BaseRequest' => $this->baseRequest,
                'Count' => count($this->groupList),
                'List' => $list
            ]
        ])->getBody()->getContents();

        $result = json_decode($content, true);

        $groupMembers = [];
        $encry = [];
        foreach ($result['ContactList'] as $group) {
            $gid = $group['UserName'];
            $members = $group['MemberList'];
            $groupMembers[$gid] = $members;
            $encry[$gid] = $group['EncryChatRoomId'];
        }
        $this->groupMembers = $groupMembers;
        $this->encryChatRoomId = $encry;
    }

    # 消息处理（第二期再分开到另一个类）

    public function processMsg()
    {
        $this->testSyncCheck();
        while (true){
            list($retCode, $selector) = $this->syncCheck();

            if(in_array($retCode, ['1100', '1101'])){ # 微信客户端上登出或者其他设备登录
                break;
            }elseif ($retCode == '0'){
                if($selector == 2){
                    $msg = $this->sync();
                    if($msg !== null){
                        $this->handleMsg($msg);
                    }
                }
            }
        }
    }

    /**
     * 测试域名同步
     *
     * @return bool
     */
    public function testSyncCheck()
    {
        foreach (['webpush.', 'webpush2.'] as $host) {
            $this->syncHost = $host . $this->baseHost;
            $retCode = $this->syncCheck()[0];

            if($retCode == 0){
                return true;
            }

            return false;
        }
    }

    public function syncCheck()
    {
        $url = 'https://' . $this->syncHost . '/cgi-bin/mmwebwx-bin/synccheck?' . http_build_query([
            'r' => time(),
            'sid' => $this->sid,
            'uin' => $this->uin,
            'skey' => $this->skey,
            'deviceid' => $this->deviceId,
            'synckey' => $this->syncKeyStr,
            '_' => time()
        ]);

        try{
            $content = $this->client->get($url, [
                'connect_timeout' => 60
            ])->getBody()->getContents();

            preg_match('/window.synccheck=\{retcode:"(\d+)",selector:"(\d+)"\}/', $content, $matches);

            return [$matches[1], $matches[2]];
        }catch (Exception $e){
            return [-1, -1];
        }
    }

    public function sync()
    {
        $url = sprintf($this->baseUri . '/webwxsync?sid=%s&skey=%s&lang=en_US&pass_ticket=%s', $this->sid, $this->skey, $this->passTicket);

        try{
            $content = $this->client->post($url, [
                'json' => [
                    'BaseRequest' => $this->baseRequest,
                    'SyncKey' => $this->syncKey,
                    'rr' => ~time()
                ],
                'connect_timeout' => 60
            ])->getBody()->getContents();

            $result = json_decode($content, true);

            if($result['BaseResponse']['Ret'] == 0){
                $this->generateSyncKey($result);
            }

            return $result;
        }catch (\Exception $e){
            return null;
        }
    }

    public function handleMsg($message)
    {
        foreach ($message['AddMsgList'] as $msg) {
            $user = ['id' => $msg['FromUserName'], 'name' => 'unknown'];
            if($msg['MsgType'] == 51){
                $msgTypeId = 0;
                $user['name'] = 'system';
            }elseif ($msg['MsgType'] == 37){
                $msgTypeId = 37;
                continue;
            }elseif ($msg['FromUserName'] === $this->myAccount['UserName']){
                $msgTypeId = 1;
                $user['name'] = 'self';
            }elseif ($msg['ToUserName'] === 'filehelper'){
                $msgTypeId = 2;
                $user['name'] = 'file_helper';
            }elseif (substr($msg['FromUserName'], 0, 2) === '@@'){
                $msgTypeId = 3;
                $user['name'] = $this->getContactPreferName($this->getContactName($user['id']));
            }elseif ($this->isBelong($msg['FromUserName'], $this->contactList)){
                $msgTypeId = 4;
                $user['name'] = $this->getContactPreferName($this->getContactName($user['id']));
            }elseif($this->isBelong($msg['FromUserName'], $this->publicList)){
                $msgTypeId = 5;
                $user['name'] = $this->getContactPreferName($this->getContactName($user['id']));
            }elseif($this->isBelong($msg['FromUserName'], $this->specialList)){
                $msgTypeId = 6;
                $user['name'] = $this->getContactPreferName($this->getContactName($user['id']));
            }else{
                $msgTypeId = 99;
            }

            $user['name'] = html_entity_decode($user['name']);

            if($this->debug && $msgTypeId !== 0){
                $this->log("[MSG] {$user['name']} :");
            }
        }
    }

    /**
     * 获取联系人名称数组
     *
     * @param $uid
     * @return array|null
     */
    public function getContactName($uid)
    {
        $info = $this->getContactInfo($uid);
        if($info === null){
            return null;
        }

        $name = [];
        $info = $info['info'];

        if($info['RemarkName']){
            $name['remarkName'] = $info['RemarkName'];
        }
        if($info['NickName']){
            $name['nickname'] = $info['NickName'];
        }
        if($info['DisplayName']){
            $name['displayName'] = $info['DisplayName'];
        }

        return count($name) === 0 ? null : $name;
    }

    /**
     * 根据ID获取用户信息
     *
     * @param $uid
     * @return null
     */
    public function getContactInfo($uid)
    {
        return $this->accountInfo['normalMember'][$uid] ?? null;
    }

    /**
     * 根据优先级获取用户名称
     *
     * @param $name
     * @return null
     */
    public function getContactPreferName($name)
    {
        if(!$name){
            return null;
        }

        return $name['remarkName'] ?? $name['nickname'] ?? $name['displayName'] ?? null;
    }

    /**
     * 是否联系人
     *
     * @param $uid
     * @param $list
     * @return bool
     */
    public function isBelong($uid, $list)
    {
        foreach ($list as $contact) {
            if($uid === $contact['UserName']){
                return true;
            }
        }

        return false;
    }

    public function extractMsgContent($msgTypeId, $msg)
    {
        $content = html_entity_decode($msg['Content']);

        $msgContent = [];

        if($msgTypeId === 0){
            return ['type' => 11, 'data' => ''];
        }elseif($msgTypeId === 2){
            return ['type' => 0, 'data' => str_replace('<br/>', '\n', $content)];
        }elseif ($msgTypeId === 3){
            $spilt = explode('<br/>', $content, 2);
            $uid = substr($spilt[0], 0, -1);
            $content = str_replace('<br/>', '', $content);
            $name = $this->getContactPreferName($this->getContactName($uid));
            $name = $name ?? $this->getGroupMemberPreferName($this->getGroupMemberName($msg['FromUserName'], $uid)) ?? 'unknown';

            $msgContent['user'] = ['id' => $uid, 'name' => $name];
        }

        $msgPrefix = isset($msgContent['user']) ? $msgContent['user']['name'] . ':' : '';


    }

    public function getGroupMemberPreferName($name)
    {
        if(!$name){
            return null;
        }

        return $name['remarkName'] ?? $name['displayName'] ?? $name['nickname'] ?? null;
    }

    /**
     * 获取群聊用户名称
     *
     * @param $gid
     * @param $uid
     * @return array|null
     */
    public function getGroupMemberName($gid, $uid)
    {
        if(!isset($this->groupMembers)){
            return null;
        }

        $group = $this->groupMembers[$gid];
        foreach ($group as $member) {
            if($member['UserName'] === $uid){
                $name = [];
                if($member['RemarkName']){
                    $name['remarkName'] = $member['RemarkName'];
                }
                if($member['NickName']){
                    $name['nickname'] = $member['NickName'];
                }
                if($member['DisplayName']){
                    $name['displayName'] = $member['DisplayName'];
                }
                return $name;
            }
        }

        return null;
    }

    private function debug($content)
    {
        file_put_contents($this->tmpPath . 'debug.json', $content);
    }

    private function log($msg)
    {
        echo $msg . PHP_EOL;
    }

    public function __get($value)
    {
        return $this->$value;
    }

}