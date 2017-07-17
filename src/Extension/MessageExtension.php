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

    /**
     * 业务扩展.
     *
     * @var array
     */
    protected $serviceExtensions = [];

    /**
     * 基础扩展.
     *
     * @var array
     */
    public $baseExtensions = [];

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    /**
     * 读取业务消息拓展.
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
            $this->addServiceExtension($extension);
        }

        $this->serviceExtensions = array_unique($this->serviceExtensions);
    }

    /**
     * 初始化业务拓展.
     */
    public function initServiceExtensions()
    {
        $tmpExtensions = [];

        foreach ($this->serviceExtensions as $serviceExtensions) {
            $extension = new $serviceExtensions();

            $tmpExtensions[] = $extension->init();

            $this->baseExtensions = array_merge($this->baseExtensions, $extension->baseExtensions);
        }

        $this->serviceExtensions = $tmpExtensions;
        $this->baseExtensions = array_unique($this->baseExtensions);

        $this->initBaseExtensions();
    }

    /**
     * 初始化基础扩展.
     */
    private function initBaseExtensions()
    {
        $tmpExtensions = [];

        foreach ($this->baseExtensions as $baseExtension) {
            $tmpExtensions[] = (new $baseExtension())->init();
        }

        $this->baseExtensions = $tmpExtensions;
    }

    /**
     * 执行拓展.
     *
     * @param $collection
     *
     * @return bool
     */
    public function exec($collection)
    {
        foreach ($this->serviceExtensions as $extension) {
            if ($extension->messageHandler($collection)) {
                return true;
            }
        }
    }

    /**
     * 添加业务消息拓展.
     *
     * @param $extension
     *
     * @throws ExtensionException
     */
    private function addServiceExtension($extension)
    {
        if ($extension instanceof AbstractMessageHandler) {
            throw new ExtensionException($extension.' is not extend AbstractMessageHandler');
        }

        $this->serviceExtensions[] = $extension;
    }
}
