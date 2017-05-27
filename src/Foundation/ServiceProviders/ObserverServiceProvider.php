<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Observers\BeforeMessageObserver;
use Hanson\Vbot\Observers\ExitObserver;
use Hanson\Vbot\Observers\FetchContactObserver;
use Hanson\Vbot\Observers\LoginSuccessObserver;
use Hanson\Vbot\Observers\NeedActivateObserver;
use Hanson\Vbot\Observers\Observer;
use Hanson\Vbot\Observers\QrCodeObserver;
use Hanson\Vbot\Observers\ReLoginSuccessObserver;

class ObserverServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Hanson\Vbot\Foundation\Vbot $vbot
     */
    public function register(Vbot $vbot)
    {
        $vbot->singleton('observer', function () use ($vbot) {
            return new Observer($vbot);
        });
        $vbot->singleton('qrCodeObserver', function () use ($vbot) {
            return new QrCodeObserver($vbot);
        });
        $vbot->singleton('loginSuccessObserver', function () use ($vbot) {
            return new LoginSuccessObserver($vbot);
        });
        $vbot->singleton('reLoginSuccessObserver', function () use ($vbot) {
            return new ReLoginSuccessObserver($vbot);
        });
        $vbot->singleton('exitObserver', function () use ($vbot) {
            return new ExitObserver($vbot);
        });
        $vbot->singleton('fetchContactObserver', function () use ($vbot) {
            return new FetchContactObserver($vbot);
        });
        $vbot->singleton('beforeMessageObserver', function () use ($vbot) {
            return new BeforeMessageObserver($vbot);
        });
        $vbot->singleton('needActivateObserver', function () use ($vbot) {
            return new NeedActivateObserver($vbot);
        });
    }
}
