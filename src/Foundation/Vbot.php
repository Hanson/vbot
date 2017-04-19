<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/9
 * Time: 21:22
 */

namespace Hanson\Vbot\Foundation;


use Hanson\Vbot\Core\Server;
use Hanson\Vbot\Support\Console;
use Hanson\Vbot\Support\Path;
use Illuminate\Support\Collection;
use Pimple\Container;

/**
 * Class Robot
 * @package Hanson\Vbot\Foundation
 * @property Server $server
 */
class Vbot
{

    public function __construct($config)
    {
        $this->setConfig($config);
    }

    /**
     * 设置Config
     *
     * @param $config
     */
    private function setConfig($config)
    {
        Config::getInstance($config);
    }
}