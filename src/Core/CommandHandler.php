<?php


namespace Hanson\Vbot\Core;


use Swoole\Process;

class CommandHandler
{

    public function handle(Process $process)
    {
        $data = explode(',', $process->read());

        $command = $data[0];
        unset($data[0]);

        switch ($command) {
            case 'send':
                $type = $data[1];
                unset($data[1]);
                $type::send(...$data);
                break;
            case 'list':
                $type = $data[1];
//                $server->send(current($server->connections), 'hi');
                $process->write(vbot($type)->toJson());
                break;
        }
    }
}