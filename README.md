<p align="center">
<a href="http://hanc.cc"><img src="https://img.shields.io/badge/contact-@HanSon-orange.svg?style=flat"></a>
<img src="https://img.shields.io/badge/license-MIT-green.svg?style=flat">
</p>

<p align="center">
  <b>Special thanks to the generous sponsorship by:</b>
  <br><br>
  <a target="_blank" href="https://www.yousails.com">
    <img src="https://yousails.com/banners/brand.png" width=350>
  </a>
</p>

## 安装

### 环境要求

- PHP >= 7.0
- [PHP fileinfo 拓展](http://php.net/manual/en/book.fileinfo.php) 储存文件需要用到
- [PHP gd 拓展](http://php.net/manual/en/book.image.php) 控制台显示二维码
- [PHP posix 拓展](http://www.php.net/manual/en/book.posix.php) 控制台显示二维码(linux)
- [PHP 系统命令 拓展](https://secure.php.net/manual/en/book.exec.php) 执行clear命令
- [PHP SimpleXML 拓展](https://secure.php.net/manual/en/book.simplexml.php) 解析XML

### 安装

**请确保已经会使用composer！**

**运行微信账号的语言设置务必设置为简体中文！！否则可能出现未知的错误！**

1、git

```
git clone https://github.com/HanSon/vbot.git
cd vbot
composer install
```

2、composer

```
composer require hanson/vbot
```

### 运行

正常运行

``` php example/index.php ```
  
带session运行

``` php example/index.php --session yoursession```

关于session ： 

带session运行会自动寻找设定session指定的cookies，如不存在则新建一个文件夹位于 `/tmp/session` 中，当下次修改代码时再执行就会免扫码自动登录。

如果不设置，vbot会自动设置一个6位的字符的session值，下次登录也可以直接设定此值进行面扫码登录。
 
PS:运行后二维码将保存于设置的缓存目录，命名为qr.png，控制台也会显示二维码，扫描即可（linux用户请确保已经打开ANSI COLOR）

*警告！执行前请先查看`index.php`的代码，注释掉你认为不需要的代码，避免对其他人好友造成困扰*

**请在terminal运行！请在terminal运行！请在terminal运行！**


## 目录结构

- vbot
  - demo (vbot 当前在运行的代码，也欢迎大家提供自己的一些实战例子)
  - example (较为初级的实例)
  - src (源码)
  - tmp (假设缓存目录设置在此)
    - session
      - hanson (设定值 `php index.php --session hanson`)
      - 523eb1 (随机值)
    - users
      - 23534234345 (微信账号的UIN值)
        - file (文件)
        - gif (表情)
        - jpg (图片)
        - mp3 (语音)
        - mp4 (视频)
        - contact.json (联系人 debug模式下存在)
        - group.json (群组 debug模式下存在)
        - member.json (所有群的所有成员 debug模式下存在)
        - official.json (公众号 debug模式下存在)
        - special.json (特殊账号 debug模式下存在)
        - message.json (消息)

## 体验

<img src="https://ws2.sinaimg.cn/large/685b97a1gy1fdordpa0cgj20e80e811z.jpg" height="320">

扫码后，验证输入“echo”即可自动加为好友并且拉入vbot群。

vbot并非24小时执行，有时会因为开发调试等原因暂停功能。如果碰巧遇到关闭情况，可加Q群 492548647 了解开放时间。执行后发送“拉我”即可自动邀请进群。

vbot示例源码为 https://github.com/HanSon/vbot/tree/master/demo/vbot.php


## 文档

详细文档在[wiki](https://github.com/HanSon/vbot/wiki)中

### 小DEMO

[vbot 实例](demo/vbot.php)

[购书半自动处理](http://t.laravel-china.org/laravel-tutorial/5.1/buy-it)

[轰炸消息到某群名](example/group.php)

[消息转发](example/forward.php)

[自定义处理器](example/custom.php)

[一键拜年](example/bainian.php)


### 基本使用

```
// 图灵API自动回复
require_once __DIR__ . './../vendor/autoload.php';

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Entity\Message;
use Hanson\Vbot\Message\Entity\Text;

$robot = new Vbot([
    'user_path' => '/path/to/tmp/', # 用于生成登录二维码以及文件保存
    'debug' => true # 用于是否输出用户组的json
]);

$robot->server->setMessageHandler(function($message){
    // 文字信息
    if ($message instanceof Text) {
        /** @var $message Text */
        // 联系人自动回复
        if ($message->fromType === 'Contact') {
            return 'hello vbot';
            // 群组@我回复
        } elseif ($message->fromType === 'Group' && $message->isAt) {
            return 'hello everyone';
        }
    }
});

$robot->server->run();

```

## to do list

vbot 已实现以及待实现的功能列表 [点击查看](https://github.com/HanSon/vbot/wiki/todolist)

## 参考项目

[lbbniu/WebWechat](https://github.com/lbbniu/WebWechat)

[littlecodersh/ItChat](https://github.com/littlecodersh/ItChat) 

感谢楼上两位作者曾对本人耐心解答

[liuwons/wxBot](https://github.com/liuwons/wxBot) 参考了整个微信的登录流程与消息处理

## 贡献者

排名不分先后，时间排序

[leo108](https://github.com/leo108) & [zhuanxuhit](https://github.com/zhuanxuhit)  terminal显示二维码 [php-console-qrcode](https://github.com/leo108/php-console-qrcode)

[littlecodersh](https://github.com/littlecodersh) 分次加载好友数量方案

[yuanshi2016](https://github.com/yuanshi2016) 分次加载好友数量方案、登录域名方案以及测试

## Q&A

常见问题[点击查看](https://github.com/HanSon/vbot/wiki/Q&A)

有问题或者建议都可以提issue

或者加入vbot的QQ群：492548647

## donate 名单


vbot 的发展离不开大家的支持，无论是star或者donate，本人都衷心的感谢各位，也会尽自己的绵薄之力把 vbot 做到最好。

donate 名单 （排名按时间倒序）

|捐助者|金额|
|-----|----|
|匿名| ￥200|
|[KimiChen](https://github.com/KimiChen)|￥188|
|倪好 | ￥88 * 2|
|[桥边红药的博客](https://www.96qbhy.com)|￥21|
|匿名| ￥6.66|
|[liuhui5354](https://github.com/liuhui5354)|￥6.66|
|匿名| ￥6.66|
|匿名| ￥50.00|
|[bestony](https://github.com/bestony)|￥10.24|
|匿名| ￥8.88|
|[haidaofei](https://github.com/haidaofei)|￥88.00|
|[kyaky](https://github.com/kyaky)|￥16.66|
|[justmd5](https://github.com/justmd5)|￥10.00|
|匿名| ￥20.00|
|匿名| ￥88.88|
|[:bear:Neo](https://github.com/Callwoola)|￥6.66|
|[lifesign](https://github.com/lifesign)|￥66.66|
|[口语猫](http://www.kouyumao.com/)|￥50.00|
|[Laravist - 最好的 Laravel 视频教程](https://www.laravist.com)| ￥66.66|
|[xingchenboy](https://github.com/xingchenboy)| ￥28.80|
|匿名| ￥6.66|
|[包菜网](http://baocai.us)| ￥16.88|
|[BEIBEI123](https://github.com/beibei123)| ￥28.88|
|[Steven Lei](https://github.com/stevenlei)| ￥88|
|9688| ￥8.88|
|[kisexu](https://github.com/kisexu)| ￥88|
|匿名的某师兄| ￥181.80|
|[summer](https://github.com/summerblue) 以及这是用vbot实现的半自动购书流程[Laravel 入门教程(推荐)](http://t.laravel-china.org/laravel-tutorial/5.1/buy-it)|￥66.66|
|A梦|￥18.88 * 4 |
|[toby2016](https://github.com/toby2016)|￥5|

打赏时请记得备注上你的github账号或者其他链接，谢谢支持！

<img src="https://ww2.sinaimg.cn/large/685b97a1gy1fd61orxreaj20yf19fmz1.jpg" height="320"><img src="https://ww2.sinaimg.cn/large/685b97a1gy1fd61qscynwj20ng0zk0tx.jpg" height="320">
