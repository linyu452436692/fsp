<?php
return [
    'test' => array(
        'driver' => 'mysql',
        'host' => '172.16.1.47',
        'database' => 'test',
        'username' => 'root',
        'password' => '273273',
        'charset' => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix' => ''
    ),
    'user' => array(
        'driver' => 'mysql',
        'read' => [
            'host' => '172.16.1.47',
        ],
        'write' => [
            'host' => '172.16.1.47'
        ],
        'database' => 'test',
        'username' => 'root',
        'password' => '273273',
        'charset' => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix' => ''
    )
];