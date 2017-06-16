<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Extension\MessageExtension;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;

class ExtensionServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $vbot)
    {
        $vbot->singleton('messageExtension', function () use ($vbot) {
            return new MessageExtension($vbot);
        });
    }
}
