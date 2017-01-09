
# 安装

## 环境要求

* PHP >= 7(代码中使用了一些PHP7的特性)

## 安装

```
composer require hanson/robot
```


# 文档

## 例子

[自动回复](https://github.com/HanSon/vbot/blob/before/example/tuling.php)

[红包提醒](https://github.com/HanSon/vbot/blob/before/example/hongbao.php)

[轰炸群](https://github.com/HanSon/vbot/blob/before/example/groups.php)

[发送消息到某群名](https://github.com/HanSon/vbot/blob/before/example/group.php)

[消息转发](https://github.com/HanSon/vbot/blob/before/example/forward.php)

[自定义处理器](https://github.com/HanSon/vbot/blob/before/example/custom.php)

[是否@了我](https://github.com/HanSon/vbot/blob/before/example/is_at.php)


## 基本使用

```
# 图灵API自动回复
require_once __DIR__ . './../vendor/autoload.php';

use Hanson\Robot\Foundation\Robot;
use Hanson\Robot\Message\Message;

$robot = new Robot([
    'tmp' => '/path/to/tmp/', # 用于生成登录二维码以及文件保存
    'debug' => true # 用于是否输出用户组的json
]);

$robot->server->setMessageHandler(function($message){
    if($message->type === 'Text'){
        $url = 'http://www.tuling123.com/openapi/api';

        $result = http()->post($url, [
            'key' => 'your tuling api key',
            'info' => $message->content
        ], true);

        return $result['text'];
    }
});

$robot->server->run();

```


# API

## server
```
# 消息处理处，接收到微信消息时的处理器
$robot->server->setMessageHandler(function($message){
    
});
```

```
# 自定义处理器，一直执行
$robot->server->setCustomHandler(function(){
    
});
```

## Message

### 属性

| 类型 | 名称 |  解释 |
| --- | --- | --- |
| array|  from |  消息来源  |
| array| sender|  当消息来自于群组时，from为群组 ，而sender为消息发送者， 假若不为 group，sender  为空 |
| string| username|   消息来源的username   |
| array|  to |  消息接收者，一般为自己   |
|  string |  content |  经过处理的消息内容 |
|   carbon  |  time |  消息接收的的时间  |
|  string |  fromType |   消息发送者的类型  |
|  string |  type |  消息内容的类型  |

###   方法 

`bool send($word, $username)`
发送消息给username的用户或者群组
*  参数 
    * `string` `word`  回复的文字 
    * `stirng` `username` 用户或者群组的username 

### type 消息类型 
*  `Text`   文字消息 
*  `Location`  位置 
*  ` Image`   图片 
*  `Voice`   语音 
*  ` AddUser`   添加朋友 
*  `Recommend`   推荐名片 
*  ` Animation `  
*  `Share`   链接分享 
*  `Video`   小视频 
*  `VideoCall`   视频聊天 
*  `Redraw`  
*  `RedPacket`    红包 
*  `Unknown`    未知  

### fromType  消息发送者类型 
*  ` System` 系统消息 
*  `FriendRequest`   加好友申请 
*  ` Self`    自己 
*  `FileHelper`    文件助手  
*  ` Group`    群组 
*  `Contact`    联系人  
*  `Official`     公众号 
*  `Special` qq邮件， 微信团队 ， 漂流瓶等特殊账号 
*  `Unknown`    未知

### 账号

无论是group, contact都有多个账号组成，而账号组成如下
```
{
  "@d5b4e97cd7bdf68152393e8e6c30ab67ba57e8fa57b4fcb5917490407c93fb06": {
    "Uin": 0,
    "UserName": "@d5b4e97cd7bdf68152393e8e6c30ab67ba57e8fa57b4fcb5917490407c93fb06",
    "NickName": "wendy",
    "HeadImgUrl": "/cgi-bin/mmwebwx-bin/webwxgeticon?***",
    "ContactFlag": 3,
    "MemberCount": 0,
    "MemberList": [],
    "RemarkName": "",
    "HideInputBarFlag": 0,
    "Sex": 0,
    "Signature": "",
    "VerifyFlag": 0,
    "OwnerUin": 0,
    "PYInitial": "WENDY",
    "PYQuanPin": "wendy",
    "RemarkPYInitial": "",
    "RemarkPYQuanPin": "",
    "StarFriend": 0,
    "AppAccountFlag": 0,
    "Statues": 0,
    "AttrStatus": 135269,
    "Province": "",
    "City": "",
    "Alias": "",
    "SnsFlag": 17,
    "UniFriend": 0,
    "DisplayName": "",
    "ChatRoomId": 0,
    "KeyWord": "",
    "EncryChatRoomId": ""
  }
}
```
最重要的信息为

| 字段名 | 含义|
| --- | --- | 
| UserName|  每个账号唯一的ID，每次登录随机生成 |
| NickName|  账号的昵称|  
| Alias|  微信号|  


## 全局方法

本库用了大量的单例模式，为了方便写了一些方便的[全局方法](https://github.com/HanSon/vbot/blob/before/src/Support/helpers.php)，contact,group,member等均继承了[illuminate/support/Collection](https://github.com/illuminate/support/blob/master/Collection.php)
相关文档： [中文文档](https://laravel-china.org/docs/5.3/collections) [英文文档](https://laravel.com/docs/5.3/collections)

### account()

#### 属性

| 类型 | 名称 |  解释 |
| --- | --- | --- |
| Hanson\Robot\Collections\Group|  group |  群组 |
| Hanson\Robot\Collections\Contact|  contact|  联系人 |

#### 方法

`getAccount($username)` 根据username返回账号
`返回值 array`

| 参数名 | 类型 | 解释 |
| ------ | ---- | ---- |
| username| string | 账号数组 |

### contact()

#### 方法

`getContactByUsername($username)` 根据username获取Contact
`返回值 array`

| 参数名 | 类型 | 解释 |
| ------ | ---- | ---- |
| username| string | 联系人的username|

`getContactById($id)` 根据微信号获取Contact
`返回值 array`

| 参数名 | 类型 | 解释 |
| ------ | ---- | ---- |
| id| string | 联系人的微信号|

`getUsernameById($id)` 根据微信号获取 username
`返回值 array`

| 参数名 | 类型 | 解释 |
| ------ | ---- | ---- |
| id| string | 联系人的微信号|

### group()

#### 方法

`isGroup($userName)` 根据username判断是否群组
`返回值 bool`

| 参数名 | 类型 | 解释 |
| ------ | ---- | ---- |
| username| string | 联系人的username|

`getGroupsByNickname($name, $blur = false, $onlyUsername = false)` 根据名称筛选群组
`返回值 array`

| 参数名 | 类型 | 解释 |
| ------ | ---- | ---- |
| name| string | 需要筛选的名称|
| blur| bool | 是否模糊匹配|
| onlyUsername | bool | 是否只筛选出username|

### member()

#### 方法

`getMemberByUsername($username)` 根据username获取成员
`返回值 array`

| 参数名 | 类型 | 解释 |
| ------ | ---- | ---- |
| name| string | 成员的username|

### myself()

#### 属性

* userName
* nickname 昵称
* sex 性别（0-女 1-男）

### http()

#### 方法

`get($url, array $query = [])` ajax get请求
`返回值 string`

| 参数名 | 类型 | 解释 |
| ------ | ---- | ---- |
| url| string | 请求链接|
| query| array | 请求参数数组|

`post($url, array $query = [], $json = false)` ajax post请求
`返回值 string|array`

| 参数名 | 类型 | 解释 |
| ------ | ---- | ---- |
| url| string | 请求链接|
| query| array | 请求参数数组|
| array| bool | 是否进行json_decode处理|

`json($url, array $query = [], $json = false)` ajax post json请求
`返回值 string|array`

| 参数名 | 类型 | 解释 |
| ------ | ---- | ---- |
| url| string | 请求链接|
| query| array | 请求参数数组|
| array| bool | 是否进行json_decode处理|

# 特别感谢

[liuwons/wxBot](https://github.com/liuwons/wxBot) 参考了整个微信的登录流程与消息处理

[overtrue/wechat](https://github.com/overtrue/wechat) 参考了部分代码的书写格式与设计思路

# to do list

- [ ] 命令行操作信息发送

- [x] 增加消息集合存储

- [ ] 消息发送
  - [x] 发送文字
  - [ ] 发送图片
  - [ ] 发送表情
  
- [ ] 消息处理
  - [x] 文字
  - [x] 图片
  - [x] 语音
  - [x] 位置
  - [x] 撤回
  - [ ] 好友验证
  - [ ] 名片
  - [ ] 表情
  - [ ] 分享
  - [ ] 视频
  
# 已知bug

* 20% 的几率初始化失败（暂时无解，如清楚问题欢迎PR）
