<?php

namespace Hanson\Vbot\Commands;

use Swoole\Client;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendCommand extends SymfonyCommand
{
    protected function configure()
    {
        $this->setName('vbot:send')
            ->addArgument('type', InputArgument::REQUIRED)
            ->addArgument('username', InputArgument::REQUIRED)
            ->addArgument('content', InputArgument::REQUIRED)
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = [];

        $data[] = 'send';
        $data[] = $this->validateType($input);
        $data[] = $input->getArgument('username');
        $data[] = $input->getArgument('content');

        $this->send(implode(',', $data));
    }

    private function validateType(InputInterface $input)
    {
        $type = $input->getArgument('type');

        $class = '\\Hanson\\Vbot\\Message\\'.ucfirst($type);

        if (!class_exists($class)) {
            throw new \Exception('type not exist.');
        }

        if (!method_exists(new $class(), 'send')) {
            throw new \Exception('method not exist.');
        }

        return $class;
    }

    private function send($data)
    {
        $client = new Client(SWOOLE_SOCK_TCP);

        if (!$client->connect('127.0.0.1', 9501, -1)) {
            exit("connect failed. Error: {$client->errCode}\n");
        }
        $client->send($data);

        $client->close();
    }
}
