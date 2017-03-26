<?php


namespace Hanson\Vbot\Support;


class Path
{

    /**
     * 设置config配置
     *
     * @param $config
     * @return mixed
     * @throws \Exception
     */
    public static function setConfig(array &$config)
    {
        if (!isset($config['tmp'])) {
            throw new \Exception('请设置缓存目录！');
        }

        $session = $config['session'] ?? null;

        $config['session'] = $config['tmp'] . DIRECTORY_SEPARATOR . 'session';

        if($session){
            if(!is_dir($config['session'] . DIRECTORY_SEPARATOR . $session)){
                mkdir($config['session'] . DIRECTORY_SEPARATOR . $session, 0700, true);
            }
        }else{
            $session = bin2hex(random_bytes(3));
            mkdir($config['session'] . DIRECTORY_SEPARATOR . $session, 0700, true);
        }

        Console::log('key' . $session);
        $config['key'] = $session;
        $config['tmp'] = realpath($config['tmp'] . DIRECTORY_SEPARATOR . 'users') . DIRECTORY_SEPARATOR;
        $config['session'] = realpath($config['session'] . DIRECTORY_SEPARATOR . $session) . DIRECTORY_SEPARATOR;

        return $config;
    }

    /**
     * 获取当前session路径
     *
     * @return string
     */
    public static function getCurrentSessionPath(): string
    {
        return server()->config['session'];
    }

    /**
     * 获取当前用户资源路径
     *
     * @return string
     */
    public static function getCurrentUinPath() :string
    {
        return server()->config['tmp'] . myself()->uin . DIRECTORY_SEPARATOR;
    }

}