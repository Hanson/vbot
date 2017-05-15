<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Api\ApiHandler;
use Hanson\Vbot\Api\Search;
use Hanson\Vbot\Api\Send;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;

class ApiServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Hanson\Vbot\Foundation\Vbot $vbot
     */
    public function register(Vbot $vbot)
    {
        $vbot->singleton('api', function () use ($vbot) {
            return new ApiHandler($vbot);
        });
        $vbot->singleton('apiSend', function () use ($vbot) {
            return new Send($vbot);
        });
        $vbot->singleton('apiSearch', function () use ($vbot) {
            return new Search($vbot);
        });
    }
}
