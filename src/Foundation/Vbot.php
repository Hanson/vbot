<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/9
 * Time: 21:22
 */

namespace Hanson\Vbot\Foundation;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;

/**
 * Class Vbot
 * @package Hanson\Vbot\Foundation
 * @property \Hanson\Vbot\Core\Server $server
 * @property \Hanson\Vbot\Exceptions\Handler $exception
 * @property \Hanson\Vbot\Support\Log $log
 * @property \Illuminate\Config\Repository $config
 */
class Vbot extends Container
{

    /**
     * Service Providers.
     *
     * @var array
     */
    protected $providers = [
        ServiceProviders\LogServiceProvider::class,
        ServiceProviders\ServerServiceProvider::class,
        ServiceProviders\ExceptionServiceProvider::class,
        ServiceProviders\CacheServiceProvider::class
    ];

    public function __construct(array $config)
    {
        $this->initializeConfig($config);

        (new Kernel($this))->bootstrap();
    }

    private function initializeConfig(array $config)
    {
        $this->config = new Repository($config);
    }

    /**
     * Register providers.
     */
    public function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }

    private function register(ServiceProviderInterface $instance)
    {
        $instance->register($this);
    }
}
