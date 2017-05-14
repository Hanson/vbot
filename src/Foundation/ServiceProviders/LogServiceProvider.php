<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Support\Log;
use Monolog\Handler\StreamHandler;

class LogServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $vbot)
    {
        $vbot->singleton('log', function () use ($vbot) {
            $log = new Log('vbot');

            return $log;
        });

        $vbot->singleton('messageLog', function () use ($vbot) {
            $log = new Log('message');

            return $log;
        });
    }
}
