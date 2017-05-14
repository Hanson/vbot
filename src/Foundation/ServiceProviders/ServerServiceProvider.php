<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Core\Server;
use Hanson\Vbot\Core\Swoole;
use Hanson\Vbot\Core\Sync;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;

class ServerServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $vbot)
    {
        $vbot->singleton('server', function () use ($vbot) {
            return new Server($vbot);
        });
        $vbot->singleton('swoole', function () use ($vbot) {
            return new Swoole($vbot);
        });
        $vbot->singleton('sync', function () use ($vbot) {
            return new Sync($vbot);
        });
    }
}
