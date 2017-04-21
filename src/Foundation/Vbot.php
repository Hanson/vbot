<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/9
 * Time: 21:22.
 */

namespace Hanson\Vbot\Foundation;

use Hanson\Vbot\Core\Server;
use Hanson\Vbot\Support\Console;
use Hanson\Vbot\Support\Path;
use Illuminate\Support\Collection;
use Pimple\Container;

/**
 * Class Robot.
 *
 * @property Server $server
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
    ];

    public function __construct($config)
    {
        parent::__construct();

        $this->setConfig($config);

        $this->registerProviders();
    }

    /**
     * 设置Config.
     *
     * @param $config
     */
    private function setConfig($config)
    {
        $config = array_merge($config, Console::getParams());

        $this->setPath($config);

        $this['config'] = function () use ($config) {
            return new Collection($config);
        };
    }

    /**
     * 设置session目录以及.
     *
     * @param $config
     *
     * @throws \Exception
     *
     * @return mixed
     */
    private function setPath(&$config)
    {
        Path::setConfig($config);
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
     * @param mixed  $value
     */
    public function __set($id, $value)
    {
        $this->offsetSet($id, $value);
    }
}
