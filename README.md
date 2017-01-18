
# 安装

## 环境要求

* PHP >= 7(代码中使用了一些PHP7的特性)

## 安装

```
composer require hanson/robot
```

# 文档

详细文档在[wiki](https://github.com/HanSon/vbot/wiki)中

## 例子

[所有类型例子](https://github.com/HanSon/vbot/blob/master/example/index.php)

[红包提醒](https://github.com/HanSon/vbot/blob/master/example/hongbao.php)

[轰炸消息到某群名](https://github.com/HanSon/vbot/blob/master/example/group.php)

[消息转发](https://github.com/HanSon/vbot/blob/master/example/forward.php)

[自定义处理器](https://github.com/HanSon/vbot/blob/master/example/custom.php)


## 基本使用

```
// 图灵API自动回复
require_once __DIR__ . './../vendor/autoload.php';

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Robot\Message\Message;

$robot = new Vbot([
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

# 特别感谢

感谢以上两位作者曾对本人耐心解答

# to do list

- [ ] 消息处理
  - [x] 文字
  - [x] 图片
  - [x] 语音
  - [x] 位置
  - [x] 视频
  - [x] 撤回
  - [x] 表情
  - [x] 红包
  - [x] 转账
  - [x] 名片
  - [x] 好友验证
  - [ ] 分享
  - [ ] 小程序
  
- [x] 消息存储
  - [x] 语音
  - [x] 图片
  - [x] 视频
  - [x] 表情

- [x] 消息发送
  - [x] 发送文字
  - [x] 发送图片
  - [x] 发送表情
  - [x] 发送视频

- [ ] 群操作
  - [ ] 创建群
  - [ ] 把某人踢出群
  - [ ] 邀请好友加入群
  - [ ] 修改群名称
  
- [ ] 好友操作
  - [ ] 给好友添加备注
  - [x] 通过好友验证

- [ ] 聊天窗口操作
  - [ ] 置顶聊天会话
  - [ ] 取消聊天会话指定
  
- [ ] 命令行操作信息发送

## 参考项目

[lbbniu/WebWechat](https://github.com/lbbniu/WebWechat)

[littlecodersh/ItChat](https://github.com/littlecodersh/ItChat) 

感谢楼上两位作者曾对本人耐心解答

[liuwons/wxBot](https://github.com/liuwons/wxBot) 参考了整个微信的登录流程与消息处理
  
## 待修复bug

* 30% 的几率初始化失败（暂时无解，如清楚问题欢迎PR）

## 问题和建议

有问题或者建议都可以提issue

或者加入我新建的QQ群：492548647