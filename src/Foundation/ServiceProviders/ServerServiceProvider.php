<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Contact\Myself;
use Hanson\Vbot\Core\BaseRequest;
use Hanson\Vbot\Core\Server;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;

class ServerServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $vbot)
    {
        $vbot->singleton('server', function () use ($vbot) {
            return new Server($vbot);
        });
//        $vbot->singleton('baseRequest', function () use ($vbot) {
//            return new BaseRequest($vbot);
//        });
        $vbot['myself'] = new Myself();
    }
}
