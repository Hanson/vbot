<?php

namespace Hanson\Vbot\Foundation\ServiceProviders;

use Hanson\Vbot\Contact\Contacts;
use Hanson\Vbot\Contact\Friends;
use Hanson\Vbot\Contact\Groups;
use Hanson\Vbot\Contact\Members;
use Hanson\Vbot\Contact\Myself;
use Hanson\Vbot\Contact\Officials;
use Hanson\Vbot\Contact\Specials;
use Hanson\Vbot\Core\ContactFactory;
use Hanson\Vbot\Foundation\ServiceProviderInterface;
use Hanson\Vbot\Foundation\Vbot;

class ContactServiceProvider implements ServiceProviderInterface
{
    public function register(Vbot $vbot)
    {
        $vbot->bind('contactFactory', function () use ($vbot) {
            return new ContactFactory($vbot);
        });
        $vbot->singleton('myself', function () use ($vbot) {
            return new Myself();
        });
        $vbot->singleton('friends', function () use ($vbot) {
            return (new Friends())->setVbot($vbot);
        });
        $vbot->singleton('groups', function () use ($vbot) {
            return (new Groups())->setVbot($vbot);
        });
        $vbot->singleton('members', function () use ($vbot) {
            return (new Members())->setVbot($vbot);
        });
        $vbot->singleton('officials', function () use ($vbot) {
            return (new Officials())->setVbot($vbot);
        });
        $vbot->singleton('specials', function () use ($vbot) {
            return (new Specials())->setVbot($vbot);
        });
        $vbot->singleton('contacts', function () use ($vbot) {
            return (new Contacts())->setVbot($vbot);
        });
    }
}
