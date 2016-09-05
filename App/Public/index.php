<?php
$beginTime = microtime(true);
define("APP_PATH", __DIR__ . '/../');
define('ENV', 'Local');
require APP_PATH . '/../vendor/autoload.php';

C('RUN_BEGIN_TIME', $beginTime);
$app = new \FES\Core\Bootstrap(require 'init.php');
$app->run();
