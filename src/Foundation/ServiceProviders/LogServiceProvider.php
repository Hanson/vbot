<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;


use Hanson\Vbot\Foundation\Config;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;


class LogServiceProvider implements ServiceProviderInterface
{

    public function register(Container $pimple)
    {
        $pimple['log'] = function ($pimple) {
            $log = new Logger('vbot');

            $log->pushHandler(new StreamHandler(
                Config::get('log.file'),
                Config::get('log.level', Logger::WARNING),
                true,
                Config::get('log.permission', null))
            );

            return $log;
        };
    }
}
