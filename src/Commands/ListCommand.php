<?php

namespace Hanson\Vbot\Commands;

use Swoole\Client;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ListCommand extends SymfonyCommand
{
    protected function configure()
    {
        $this->setName('vbot:list')->addArgument('type', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $type = $input->getArgument('type');

        $data[] = 'list';
        $data[] = $type;

        $result = $this->send(implode(',', $data));

        print_r($result);

        $io = new SymfonyStyle($input, $output);
        $io->table(['username', 'nickname'], []);
    }

    private function send($data)
    {
        $client = new Client(SWOOLE_SOCK_TCP);

        if (!$client->connect('127.0.0.1', 9501, -1)) {
            exit("connect failed. Error: {$client->errCode}\n");
        }
        echo $result = $client->send($data);

        $client->close();

        return $result;
    }
}
