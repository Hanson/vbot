<?php

namespace Hanson\Vbot\Commands;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationCommand extends SymfonyCommand
{
    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    public $capsule;

    /**
     * @var \Illuminate\Database\Schema\Builder
     */
    public $schema;

    protected function configure()
    {
        $this->setName('vbot:migration')->addArgument('config', InputArgument::REQUIRED)
            ->setDescription('create default database for vbot.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $input->getArgument('config');

        if (!is_dir($config)) {
            throw new \Exception('config path is not exist!');
        }

        require $config;

        $this->capsule = new Capsule();
        $this->capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => DB_HOST,
            'port'      => DB_PORT,
            'database'  => DB_NAME,
            'username'  => DB_USER,
            'password'  => DB_PASSWORD,
            'charset'   => 'utf8mb4',
            'collation' => 'utf8mb4_general_ci',
        ]);

        $this->schema = $this->capsule->schema();

        $this->schema->create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uin')->index();
            $table->string('username');
            $table->string('nickname');
            $table->string('avatar');
            $table->string('signature');
            $table->tinyInteger('gender');
            $table->timestamps();
        });
    }
}
