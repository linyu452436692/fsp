<?php
return [
    'test' => array(
        'driver' => 'mysql',
        'host' => '192.168.5.34',
        'database' => 'local_cache',
        'username' => 'root',
        'password' => '273273',
        'charset' => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix' => ''
    ),
    'user' => array(
        'driver' => 'mysql',
        'read' => [
            'host' => '127.0.0.1',
        ],
        'write' => [
            'host' => '127.0.0.1'
        ],
        'database' => 'test',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix' => ''
    )
];