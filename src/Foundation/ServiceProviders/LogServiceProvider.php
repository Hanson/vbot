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

            $log->pushHandler(new StreamHandler(
                $vbot['config']['log.system'],
                $vbot['config']['log.level'],
                true,
                $vbot['config']['log.permission']
            ));

            return $log;
        });

        if ($vbot->config['log.message']) {
            $vbot->singleton('messageLog', function () use ($vbot) {
                $log = new Log('message');

                $log->pushHandler(new StreamHandler(
                    $vbot['config']['log.message'],
                    $vbot['config']['log.level'],
                    true,
                    $vbot['config']['log.permission']
                ));

                return $log;
            });
        }
    }
}
