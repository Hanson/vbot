<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\Config;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;
use Illuminate\Cache\CacheManager;
use Illuminate\Cache\MemcachedConnector;
use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class CacheServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $vbot)
    {
        $vbot['files'] = new Filesystem();

        $vbot->singleton('cache', function ($app) {
            return new CacheManager($app);
        });
        $vbot->singleton('cache.store', function ($app) {
            return $app['cache']->driver();
        });
        $vbot->singleton('memcached.connector', function () {
            return new MemcachedConnector();
        });
    }
}
