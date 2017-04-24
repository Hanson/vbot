<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;
use Illuminate\Cache\CacheManager;
use Illuminate\Cache\MemcachedConnector;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Redis\Database;
use Illuminate\Redis\RedisManager;

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
        $vbot->singleton('redis', function ($vbot) {
            $config = $vbot->config['database.redis'];

            return new RedisManager(array_get($config, 'client', 'predis'), $config);
        });
        $vbot->bind('redis.connection', function ($vbot) {
            return $vbot['redis']->connection();
        });
    }
}
