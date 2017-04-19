<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;


use Hanson\Vbot\Exceptions\Handler;
use Pimple\Container;
use Pimple\ServiceProviderInterface;


class ExceptionServiceProvider implements ServiceProviderInterface
{

    public function register(Container $pimple)
    {
        $pimple['exception'] = function ($pimple) {
            return new Handler();
        };
    }
}
