<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Observers\LoginSuccessObserver;
use Hanson\Vbot\Observers\Observer;
use Hanson\Vbot\Observers\QrCodeObserver;

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
    }
}
