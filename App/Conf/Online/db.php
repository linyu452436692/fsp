<?php
return [
    'APP_CRM' => [
        'servers' => [
            [
                'host' => '127.0.0.1',
                'password' => '123456',
                'port' => 6379,
                'db' => 1,
                'weight' => 1,
                'timeout' => 1
            ]
        ]
    ],
    'APP_SETTLEMENT' => [
        'servers' => [
            [
                'host' => '127.0.0.1',
                'password' => '123456',
                'port' => 6379,
                'db' => 6,
                'weight' => 1,
                'timeout' => 1
            ]
        ]
    ]
];