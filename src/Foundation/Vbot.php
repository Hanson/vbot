<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/9
 * Time: 21:22.
 */

namespace Hanson\Vbot\Foundation;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;

/**
 * Class Vbot.
 *
 * @property \Hanson\Vbot\Core\Server $server
 * @property \Hanson\Vbot\Core\MessageHandler $message
 * @property \Hanson\Vbot\Core\Sync $sync
 * @property \Hanson\Vbot\Core\ContactFactory $contactFactory
 * @property \Hanson\Vbot\Foundation\ExceptionHandler $exception
 * @property \Hanson\Vbot\Support\Log $log
 * @property \Hanson\Vbot\Support\Http $http
 * @property \Hanson\Vbot\Console\QrCode $qrCode
 * @property \Hanson\Vbot\Console\Console $console
 * @property \Hanson\Vbot\Observers\Observer $observer
 * @property \Hanson\Vbot\Observers\QrCodeObserver $qrCodeObserver
 * @property \Hanson\Vbot\Observers\LoginSuccessObserver $loginSuccessObserver
 * @property \Hanson\Vbot\Observers\ReLoginSuccessObserver $reLoginSuccessObserver
 * @property \Hanson\Vbot\Observers\ExitObserver $exitObserver
 * @property \Hanson\Vbot\Observers\FetchContactObserver $fetchContactObserver
 * @property \Hanson\Vbot\Observers\BeforeMessageObserver $beforeMessageObserver
 * @property \Illuminate\Config\Repository $config
 * @property \Illuminate\Cache\Repository $cache
 * @property \Hanson\Vbot\Contact\Myself $myself
 * @property \Hanson\Vbot\Contact\Friends $friends
 * @property \Hanson\Vbot\Contact\Groups $groups
 * @property \Hanson\Vbot\Contact\Members $members
 * @property \Hanson\Vbot\Contact\Officials $officials
 * @property \Hanson\Vbot\Contact\Specials $specials
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
        ServiceProviders\CacheServiceProvider::class,
        ServiceProviders\HttpServiceProvider::class,
        ServiceProviders\ObserverServiceProvider::class,
        ServiceProviders\ConsoleServiceProvider::class,
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
