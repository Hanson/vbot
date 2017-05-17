<?php

namespace Hanson\Vbot\Api;

use Hanson\Vbot\Foundation\Vbot;

class ApiHandler
{
    /**
     * @var Vbot
     */
    private $vbot;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    public function handle($request)
    {
        if (!($result = $this->validate($request))) {
            return;
        }

        return $this->vbot->{$result['class']}->execute($result['params']);
    }

    private function validate($request)
    {
        $request = explode("\r\n\r\n", $request);

        if (!$request[1]) {
            return false;
        }

        $data = json_decode($request[1], true);

        if (!isset($data['action']) || !isset($data['params'])) {
            return false;
        }

        $namespace = '\\Hanson\\Vbot\\Api\\';

        if (class_exists($class = $namespace.ucfirst($data['action']))) {
            return ['params' => $data['params'], 'class' => 'api'.ucfirst($data['action'])];
        }

        return false;
    }
}
