<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/9
 * Time: 21:22
 */

namespace Hanson\Vbot\Foundation;


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
        ServiceProviders\ServerServiceProvider::class,
        ServiceProviders\ExceptionServiceProvider::class,
    ];

    public function __construct(array $config)
    {
        parent::__construct();

        Config::initConfig($config);

        $this->registerProviders();
        $this->bootstrap();
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

    private function bootstrap()
    {
        $this->bootstrapWithException();
    }

    private function bootstrapWithException()
    {
        error_reporting(-1);
        set_error_handler([$this->exception, 'handleError']);
        set_exception_handler([$this->exception, 'handleException']);
        register_shutdown_function([$this->exception, 'handleShutdown']);
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