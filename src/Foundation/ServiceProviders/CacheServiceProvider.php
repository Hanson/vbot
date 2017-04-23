<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;
use Illuminate\Cache\CacheManager;
use Illuminate\Cache\MemcachedConnector;
use Illuminate\Filesystem\Filesystem;

class CacheServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $vbot)
    {
        $vbot['files'] = new Filesystem();

        $vbot->singleton('cache', function ($vbot) {
            return new CacheManager($vbot);
        });
        $vbot->singleton('cache.store', function ($vbot) {
            return $vbot['cache']->driver();
        });
        $vbot->singleton('memcached.connector', function () {
            return new MemcachedConnector();
        });
    }
}
