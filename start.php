<?php
/**
 * run with command 
 * php start.php start
 */
require_once 'cnf/config.php';
require_once 'cnf/OdinSet.php';

ini_set('display_errors', 'on');
use Workerman\Worker;
use \GatewayWorker\Lib\Gateway;

if(strpos(strtolower(PHP_OS), 'win') === 0)
{
    exit("start.php not support windows, please use start_for_win.bat\n");
}

// 检查扩展
if(!extension_loaded('pcntl'))
{
    exit("Please install pcntl extension. See http://doc3.workerman.net/install/install.html\n");
}

if(!extension_loaded('posix'))
{
    exit("Please install posix extension. See http://doc3.workerman.net/install/install.html\n");
}

// 标记是全局启动
define('GLOBAL_START', 1);

require_once 'SocketBase/Event.php';

// 加载所有Applications/start*.php，以便启动所有服务
foreach(glob(__DIR__.'/Applications/start*.php') as $start_file)
{
    require_once $start_file;
}


function __onConnect($client_id){
    Gateway::sendToClient($client_id, json_encode(array(
          'type'      => 'init',
          'client_id' => $client_id
    )));
}
Worker::$logFile = "/var/log/php-fpm/p.log";

// 运行所有服务
Worker::runAll();