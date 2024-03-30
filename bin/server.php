<?php

use Application\App;
use Application\StartUp;
use Fluffy\Swoole\Cache\CacheManager;
use Fluffy\Swoole\Task\TaskManager;
use Swoole\Constant;

$currentDir = getcwd();

require __DIR__ . '/AppServer.php';
$config = require $currentDir . '/configs/serverConfig.php';

$config['BASE_DIR'] = $currentDir;
$config['HOST_CONFIG'] = ['BASE_DIR' => __DIR__];
$port = $config[Constant::OPTION_PORT] ?? (getenv('PORT') ?: 8101);


$appServer = new AppServer($port, $config, function (AppServer $appServer) {
    // you have autoload here
    $app = new App(new StartUp());
    $app->setUp();
    $app->setAppDependencies(new TaskManager($appServer), new CacheManager($appServer));
    $appServer->setApp($app);
});
$appServer->run();

// Get processes list:
// ps -ef
// sudo kill -9 PID

// ab -n 1000000 -c 100 -k http://127.0.0.1:8086/
// ab -n 1000000 -c 100 -k http://127.0.0.1:8086/api/authorization/session
// postgresql test
// ab -n 1000000 -c 100 -k http://127.0.0.1:8086/api/authorization/me

// ab -n 1000000 -c 100 -k http://127.0.0.1:8086/
// ab -n 1000000 -c 100 -k http://viewi.wsl.com/

// ab -n 1000000 -c 100 -k http://127.0.0.1:8086/api/authorization/session
// ab -n 1000000 -c 100 -k https://viewi.wsl.com/api/authorization/session
// ab -n 1000000 -c 100 -k https://viewi.wsl.com/api/authorization/me

// WSL
// sudo sudo service nginx status
// sudo sudo service nginx start
// sudo nginx -t
// sudo sudo service nginx stop
// sudo service postgresql start
// sudo nano /etc/nginx/sites-available/default
// sudo /etc/init.d/nginx reload

// autostart
// Create line in /etc/sudoers (at WSL to prevent asking password):

//  %sudo   ALL=(ALL) NOPASSWD: /usr/sbin/service mysql start
// Create .bat file in Windows startup directory with this line (dir find here: Win+R and shell:startup):

//  wsl sudo service mysql start
// After restarting the service, it will start automatically.


// You can configure sudo to never ask for your password.

// Open a Terminal window and type:

// sudo visudo
// In the bottom of the file, add the following line:

// $USER ALL=(ALL) NOPASSWD: ALL