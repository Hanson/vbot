<?php

namespace Hanson\Vbot\Console;

use Carbon\Carbon;
use Hanson\Vbot\Foundation\Vbot;

class Console
{
    const INFO = 'INFO';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';
    const MESSAGE = 'MESSAGE';

    /**
     * @var Vbot
     */
    protected $vbot;

    /**
     * console config.
     *
     * @var array
     */
    protected $config;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;

        $this->config = $this->vbot->config['console'];
    }

    /**
     * determine the console is windows or linux.
     *
     * @return bool
     */
    public static function isWin()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

    public static function clear()
    {
        self::isWin() ? system('cls') : system('clear');
    }

    /**
     * print in terminal.
     *
     * @param $str
     * @param string $level
     */
    public function log($str, $level = 'INFO')
    {
        if ($this->isOutput()) {
            $this->vbot->log->log($level, $str);
            echo '['.Carbon::now()->toDateTimeString().']'."[{$level}] ".$str.PHP_EOL;
        }
    }

    /**
     * print message.
     *
     * @param $str
     */
    public function message($str)
    {
        if ($this->isOutput() && array_get($this->config, 'message', true)) {
            $this->log($str, self::MESSAGE);
        }
    }

    public function isOutput()
    {
        return array_get($this->config, 'output', true);
    }

    public static function __callStatic($method, $args)
    {
    }
}
