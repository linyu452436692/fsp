<?php
return [
    //系统环境初始化后的钩子
    'init_env' => [
        'class' => [
            'name' => \App\Custom\PreEnv::class,
            'action' => 'init',
            'params' => [],
        ],
        'function' => [
            'file' => APP_PATH . 'Custom/functions.php',
            'name' => 'test',
            'params' => [],
        ],
    ],
    //执行操作前的钩子
    'pre_action' => [
        'class' => [
            'name' => \App\Custom\PreAction::class,
            'action' => 'init',
            'params' => [],
        ],
    ],
    //session初始化钩子
    'init_session' => [
        
    ],
];