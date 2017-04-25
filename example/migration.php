<?php

require 'database.php';

return [
    'paths' => [
        'migrations' => 'migrations'
    ],
    'migration_base_class' => '\Hanson\Vbot\Migrations\BaseMigration',
    'environments' => [
        'default_migration_table' => 'migrations',
        'default_database' => 'dev',
        'dev' => [
            'adapter' => 'mysql',
            'host' => DB_HOST,
            'name' => DB_NAME,
            'user' => DB_USER,
            'pass' => DB_PASSWORD,
            'port' => DB_PORT
        ]
    ]
];