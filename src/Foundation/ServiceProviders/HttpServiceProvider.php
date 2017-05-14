<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Api\ApiHandler;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Support\Http;

class HttpServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Hanson\Vbot\Foundation\Vbot $vbot
     */
    public function register(Vbot $vbot)
    {
        $vbot->singleton('http', function () use ($vbot) {
            return new Http($vbot);
        });
        $vbot->singleton('api', function () use ($vbot) {
            return new ApiHandler($vbot);
        });
    }
}
