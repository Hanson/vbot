<?php

namespace Hanson\Vbot\Example;

class Observer
{
    public static function setQrCodeObserver($qrCodeUrl)
    {
        echo $qrCodeUrl;
    }

    public static function setLoginSuccessObserver()
    {
        echo '登录成功'.PHP_EOL;
    }

    public static function setReLoginSuccessObserver()
    {
        echo '免扫码登录成功'.PHP_EOL;
    }

    public static function setExitObserver()
    {
        echo '退出程序'.PHP_EOL;
    }

    public static function setFetchContactObserver(array $contacts)
    {
        print_r($contacts['groups']);
    }

    public static function setBeforeMessageObserver()
    {
        echo '准备接收消息'.PHP_EOL;
    }
}
