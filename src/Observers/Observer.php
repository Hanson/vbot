<?php

namespace Hanson\Vbot\Observers;

use Hanson\Vbot\Exceptions\ArgumentException;
use Hanson\Vbot\Exceptions\ObserverNotFoundException;
use Hanson\Vbot\Foundation\Vbot;

/**
 * Class Observer.
 *
 * @method setQrCodeObserver($callback)
 * @method setLoginSuccessObserver($callback)
 * @method setReLoginSuccessObserver($callback)
 * @method setExitObserver($callback)
 * @method setFetchContactObserver($callback)
 * @method setNeedActivateObserver($callback)
 * @method setBeforeMessageObserver($callback)
 */
class Observer
{
    /**
     * @var Vbot
     */
    protected $vbot;

    protected $callback;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    public function trigger()
    {
        $args = func_get_args();

        if (is_callable($this->getCallback())) {
            call_user_func_array($this->getCallback(), $args);
        }
    }

    protected function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new ArgumentException('Argument #1 must be a callback in: '.get_class($this));
        }

        $this->callback = $callback;
    }

    protected function getCallback()
    {
        return $this->callback;
    }

    public function __call($method, $args)
    {
        $observerClass = lcfirst(str_replace('set', '', $method));

        if (!$observer = $this->vbot->{$observerClass}) {
            throw new ObserverNotFoundException("Observer: {$observerClass} not found.");
        }

        $observer->setCallback($args[0]);
    }
}
