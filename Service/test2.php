<?php
define("ROOT_PATH", __DIR__ . '/');
require ROOT_PATH . '/../vendor/autoload.php';

$serv = new \swoole_server("127.0.0.1", 9501);
$serv->set(array(
    'worker_num' => 8,   //工作进程数量
    'daemonize' => false, //是否作为守护进程
));
$serv->on('start', function ($serv){
    echo "Client:Start.\n";
});
$serv->on('connect', function ($serv, $fd){
    echo "Client:Connect.\n";
});
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $a = new \App\Controller\indexController();
    $data = $a->test();
    $serv->send($fd, 'Swoole: '. $data);
    echo "Client:Send.\n";
    $serv->close($fd);
});
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});
$serv->start();