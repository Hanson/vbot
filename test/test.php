<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2016/12/7
 * Time: 16:33
 */

require_once __DIR__ . './../vendor/autoload.php';

$robot = new \Hanson\Robot\Robot(['tmp' => realpath('./tmp') . '/']);
echo $robot->run();
echo 'finish';
//echo $robot->uuid;