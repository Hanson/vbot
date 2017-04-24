<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\ExceptionHandler;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;

class ExceptionServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Hanson\Vbot\Foundation\Vbot $vbot
     */
    public function register(Vbot $vbot)
    {
        $vbot->singleton('exception', function () use ($vbot) {
            return new ExceptionHandler($vbot);
        });
    }
}
