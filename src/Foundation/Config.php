<?php


namespace Hanson\Vbot\Foundation;


use Illuminate\Config\Repository;
use Pimple\Container;

class Config extends Container
{
    /**
     * Repository instance.
     *
     * @var Repository
     */
    protected static $instance;

    /**
     * init config.
     *
     * @var array
     */
    protected static $config = [];

    /**
     * init config.
     *
     * @param array $config
     */
    public static function initConfig($config = [])
    {
        static::$config = $config;
    }

    /**
     * call static function.
     *
     * @param $method
     * @param $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        if (! static::$instance) {
            static::$instance = new Repository(self::$config);
        }

        return static::$instance->{$method}(...$args);
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