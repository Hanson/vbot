<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Core\MessageFactory;
use Hanson\Vbot\Core\MessageHandler;
use Hanson\Vbot\Core\ShareFactory;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Text;

class MessageServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $vbot)
    {
        $vbot->singleton('messageHandler', function () use ($vbot) {
            return new MessageHandler($vbot);
        });
        $vbot->singleton('messageFactory', function () use ($vbot) {
            return new MessageFactory($vbot);
        });
        $vbot->singleton('shareFactory', function () use ($vbot) {
            return new ShareFactory($vbot);
        });

        //        $vbot->bind('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
//        $vbot->singleton('text', function () use ($vbot) {
//            return new Text($vbot);
//        });
    }
}
