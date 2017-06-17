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
 * Class Vbot.ShareFactory.
 *
 * @property \Hanson\Vbot\Core\Server $server
 * @property \Hanson\Vbot\Core\Swoole $swoole
 * @property \Hanson\Vbot\Core\MessageHandler $messageHandler
 * @property \Hanson\Vbot\Core\MessageFactory $messageFactory
 * @property \Hanson\Vbot\Core\ShareFactory $shareFactory
 * @property \Hanson\Vbot\Extension\MessageExtension $messageExtension
 * @property \Hanson\Vbot\Message\Text $text
 * @property \Hanson\Vbot\Core\Sync $sync
 * @property \Hanson\Vbot\Core\ContactFactory $contactFactory
 * @property \Hanson\Vbot\Foundation\ExceptionHandler $exception
 * @property \Hanson\Vbot\Support\Log $log
 * @property \Hanson\Vbot\Support\Log $messageLog
 * @property \Hanson\Vbot\Support\Http $http
 * @property \Hanson\Vbot\Api\ApiHandler $api
 * @property \Hanson\Vbot\Console\QrCode $qrCode
 * @property \Hanson\Vbot\Console\Console $console
 * @property \Hanson\Vbot\Observers\Observer $observer
 * @property \Hanson\Vbot\Observers\QrCodeObserver $qrCodeObserver
 * @property \Hanson\Vbot\Observers\NeedActivateObserver $needActivateObserver
 * @property \Hanson\Vbot\Observers\LoginSuccessObserver $loginSuccessObserver
 * @property \Hanson\Vbot\Observers\ReLoginSuccessObserver $reLoginSuccessObserver
 * @property \Hanson\Vbot\Observers\ExitObserver $exitObserver
 * @property \Hanson\Vbot\Observers\FetchContactObserver $fetchContactObserver
 * @property \Hanson\Vbot\Observers\BeforeMessageObserver $beforeMessageObserver
 * @property \Illuminate\Config\Repository $config
 * @property \Illuminate\Cache\Repository $cache
 * @property \Hanson\Vbot\Contact\Myself $myself
 * @property \Hanson\Vbot\Contact\Friends $friends
 * @property \Hanson\Vbot\Contact\Contacts $contacts
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
        ServiceProviders\MessageServiceProvider::class,
        ServiceProviders\ContactServiceProvider::class,
        ServiceProviders\ApiServiceProvider::class,
        ServiceProviders\ExtensionServiceProvider::class,
    ];

    public function __construct(array $config)
    {
        $this->initializeConfig($config);

        (new Kernel($this))->bootstrap();

        static::$instance = $this;
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
