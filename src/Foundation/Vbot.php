<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/9
 * Time: 21:22
 */

namespace Hanson\Vbot\Foundation;


use Hanson\Vbot\Support\Log;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;

/**
 * Class Vbot
 * @package Hanson\Vbot\Foundation
 * @property \Hanson\Vbot\Core\Server $server
 * @property \Hanson\Vbot\Exceptions\Handler $exception
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
    ];

    public function __construct(array $config)
    {
        parent::__construct();

        $this->initializeConfig($config);
        $this->initializeLogger();
        $this->registerProviders();

        (new Kernel($this))->bootstrap();
//        $this->bootstrap();
    }

    private function initializeConfig(array $config)
    {
        Config::initConfig($config);
    }

    private function initializeLogger()
    {
        if (Log::hasLogger()) {
            return;
        }

        $logger = new Logger('vbot');

        if (!Config::get('debug') || defined('PHPUNIT_RUNNING')) {
            $logger->pushHandler(new NullHandler());
        } elseif ($logFile = Config::get('log.file')) {
            $logger->pushHandler(new StreamHandler(
                    $logFile,
                    Config::get('log.level', Logger::WARNING),
                    true,
                    Config::get('log.permission', null))
            );
        }

        Log::setLogger($logger);
    }

    /**
     * Register providers.
     */
    private function registerProviders()
    {
        foreach ($this->providers as $provider) {
            $this->register(new $provider());
        }
    }

    /**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * Magic set access.
     *
     * @param string $id
     * @param mixed $value
     */
    public function __set($id, $value)
    {
        $this->offsetSet($id, $value);
    }
}