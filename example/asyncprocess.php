<?php

require_once __DIR__.'./../vendor/autoload.php';

use Carbon\Carbon;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Entity\Text;

$robot = new Vbot([
    'user_path' => __DIR__.'/./../tmp/',
    'session'   => 'console',
    'debug'     => true,
]);

if ($robot->server->restoreServer()) {
    Text::send('filehelper', 'Hello from console '.Carbon::now()->toDateTimeString());
}
