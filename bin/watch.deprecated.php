<?php

// php watch.php php server.php
// php watch.php /usr/bin/php server.php
// Get processes list:
// ps -ef

// $inputs = array_slice($argv, 1);
// $command = $inputs[0];
// $arguments = array_slice($inputs, 1);

// echo var_dump(ini_get('max_execution_time'));

$startTime = time();


require __DIR__ . '/core/Watcher.php';
$config = require __DIR__ . '/watchConfig.php';
$watcher = new Watcher(__DIR__, $argv, $config);
$watcher->start();

// track shutdown timing
// echo $startTime . PHP_EOL;
// echo time() . PHP_EOL;
// echo (time() - $startTime) . PHP_EOL;
