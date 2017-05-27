<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Console\Console;
use Hanson\Vbot\Console\QrCode;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;

class ConsoleServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $vbot)
    {
        $vbot->bind('qrCode', function () use ($vbot) {
            return new QrCode($vbot);
        });
        $vbot->singleton('console', function () use ($vbot) {
            return new Console($vbot);
        });
    }
}
