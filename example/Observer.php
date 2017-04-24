<?php


namespace Hanson\Vbot\Example;


class Observer
{
    public function setQrCodeObserver($qrCodeUrl)
    {
        echo $qrCodeUrl;
    }

    public function setLoginSuccessObserver()
    {
        echo '登录成功'.PHP_EOL;
    }

    public function setReLoginSuccessObserver()
    {
        echo '免扫码登录成功'.PHP_EOL;
    }

    public function setExitObserver()
    {
        echo '退出程序'.PHP_EOL;
    }
}