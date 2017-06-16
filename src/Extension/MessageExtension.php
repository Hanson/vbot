<?php

namespace Hanson\Vbot\Extension;

use Hanson\Vbot\Exceptions\ExtensionException;
use Hanson\Vbot\Foundation\Vbot;

class MessageExtension
{
    /**
     * @var Vbot
     */
    protected $vbot;

    protected $extensions = [];

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    /**
     * 读取消息拓展.
     *
     * @param $extensions
     *
     * @throws ExtensionException
     */
    public function load($extensions)
    {
        if (!is_array($extensions)) {
            throw new ExtensionException('extensions must pass an array.');
        }

        foreach ($extensions as $extension) {
            $this->addExtension($extension);
        }
    }

    /**
     * 初始化拓展.
     */
    public function initExtensions()
    {
        foreach ($this->extensions as $extension) {
            (new $extension())->init();
        }
    }

    /**
     * 执行拓展.
     *
     * @param $collection
     */
    public function exec($collection)
    {
        foreach ($this->extensions as $extension) {
            (new $extension())->messageHandler($collection);
        }
    }

    /**
     * 添加消息拓展.
     *
     * @param $extension
     *
     * @throws ExtensionException
     */
    private function addExtension($extension)
    {
        if ($extension instanceof AbstractMessageHandler) {
            throw new ExtensionException($extension.' is not extend AbstractMessageHandler');
        }

        $this->extensions[] = $extension;
    }
}
