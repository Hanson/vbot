<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Support\Lang;

class LangServiceProvider implements ServiceProviderInterface
{
    /**
     * @param \Hanson\Vbot\Foundation\Vbot $vbot
     */
    public function register(Vbot $vbot)
    {
        $vbot->singleton('lang', function () use ($vbot) {
            return new Lang($vbot);
        });
    }
}
