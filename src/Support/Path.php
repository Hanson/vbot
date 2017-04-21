<?php

namespace Hanson\Vbot\Support;

class Path
{
    /**
     * 设置config配置.
     *
     * @param $config
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public static function setConfig(array &$config)
    {
        if (!(isset($config['tmp']) || isset($config['user_path']))) {
            throw new \Exception('请设置缓存目录！');
        }

        $tempPath = $config['user_path'] ?? $config['tmp'];

        $session = $config['session'] ?? bin2hex(random_bytes(3));

        $config['session'] = $session;
        $config['user_path'] = static::getRealPath($tempPath.DIRECTORY_SEPARATOR.'users').DIRECTORY_SEPARATOR;
        $config['session_path'] = static::getRealPath($tempPath.DIRECTORY_SEPARATOR.'session'.DIRECTORY_SEPARATOR.$session).DIRECTORY_SEPARATOR;

        return $config;
    }

    /**
     * 获取当前session路径.
     *
     * @return string
     */
    public static function getCurrentSessionPath(): string
    {
        return server()->config['session_path'];
    }

    /**
     * 获取当前用户资源路径.
     *
     * @return string
     */
    public static function getCurrentUinPath(): string
    {
        return server()->config['user_path'].myself()->uin.DIRECTORY_SEPARATOR;
    }

    /**
     * 获取real path.
     *
     * @param $path
     *
     * @return string
     */
    public static function getRealPath($path)
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        return realpath($path);
    }
}
