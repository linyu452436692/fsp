<?php
define("APP_PATH", __DIR__ . '/../');
define('ENV', 'Online');
require APP_PATH . '/../vendor/autoload.php';
$app = new \FES\Core\Bootstrap(require 'init.php');
$app->run();

/* $client = new \swoole_client(SWOOLE_SOCK_TCP);
if (!$client->connect('127.0.0.1', 9501, -1))
{
    exit("connect failed. Error: {$client->errCode}\n");
}
$a = pack('N', '5') . '1245234324326';
$client->send($a);
var_dump(unpack('N', $client->recv(6, 1))[1]);
echo $client->recv();
$client->close(); */

