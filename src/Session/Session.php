<?php

namespace Hanson\Vbot\Session;

use Hanson\Vbot\Foundation\Vbot;

class Session
{
    /**
     * @var Vbot
     */
    protected $vbot;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    public function randomKey()
    {
        return bin2hex(random_bytes(3));
    }

    public function currentSession()
    {
        $arguments = getopt(null, ['session::']);

        return isset($arguments['session']) && $arguments['session'] ? $arguments['session'] :
            $this->vbot->config->get('session', $this->randomKey());
    }

    public function has($session)
    {
        return $this->vbot->cache->has('session.'.$session);
    }
}
