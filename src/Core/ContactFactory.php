<?php


namespace Hanson\Vbot\Core;


use Hanson\Vbot\Foundation\Vbot;

abstract class ContactFactory
{
    /**
     * @var Vbot
     */
    private $vbot;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    public function save($contact)
    {
        $contact = $this->create($contact);

        $this->vbot->cache->put($contact['UserName'], $contact);
    }

    abstract public function create($contact);
}