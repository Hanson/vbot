<?php

namespace Hanson\Vbot\Core;

use Hanson\Vbot\Foundation\Vbot;
use Swoole\Process;
use Swoole\Server as SwooleServer;

class Swoole
{
    /**
     * @var Vbot
     */
    private $vbot;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    public function run()
    {
        $server = new SwooleServer($this->vbot->config->get('swoole.ip', '127.0.0.1'), $this->vbot->config->get('swoole.port', 8866));

        $handleProcess = new Process(function ($worker) use (&$server) {
            $this->vbot->messageHandler->listen($server);
        });
        $handleProcess->start();

        $server->on('receive', function (SwooleServer $server, $fd, $from_id, $data) {
            $response = $this->vbot->api->handle($data);

            $response = $this->makeResponse($response);

            $server->send($fd, $response);
        });
        $server->start();
        exit;
    }

    private function makeResponse($data)
    {
        $data = json_encode($data);

        $headers = [
            'Server'         => 'Swoole',
            'Content-Type'   => 'application/json',
            'Content-Length' => strlen($data),
        ];

        $response[] = 'HTTP/1.1 200';

        foreach ($headers as $key => $val) {
            $response[] = $key.':'.$val;
        }

        $response[] = '';
        $response[] = $data;

        return implode("\r\n", $response);
    }
}
