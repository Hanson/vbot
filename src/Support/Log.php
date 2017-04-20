<?php


namespace Hanson\Vbot\Support;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;


/**
 * Class Log.
 *
 * @method static debug($message, $context = null)
 * @method static info($message, $context = null)
 * @method static notice($message, $context = null)
 * @method static warning($message, $context = null)
 * @method static error($message, $context = null)
 * @method static critical($message, $context = null)
 * @method static alert($message, $context = null)
 * @method static emergency($message, $context = null)
 */
class Log
{
    /**
     * Logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected static $logger;

    /**
     * Return the logger instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public static function getLogger()
    {
        return self::$logger ?: self::$logger = self::createDefaultLogger();
    }

    /**
     * Set logger.
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public static function setLogger(LoggerInterface $logger)
    {
        self::$logger = $logger;
    }

    /**
     * Tests if logger exists.
     *
     * @return bool
     */
    public static function hasLogger()
    {
        return self::$logger ? true : false;
    }

    /**
     * Forward call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        return forward_static_call_array([self::getLogger(), $method], $args);
    }

    /**
     * Forward call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($method, $args)
    {
        return call_user_func_array([self::getLogger(), $method], $args);
    }

    /**
     * Make a default log instance.
     *
     * @return \Monolog\Logger
     */
    private static function createDefaultLogger()
    {
        $log = new Logger('vbot');

        $log->pushHandler(new ErrorLogHandler());

        return $log;
    }
}
