<?php
//插件库
return [
    'response' => [
        'class' => \FES\Lib\Http\Response::class,
        'params' => '',
    ],
    'request' => [
        'class' => \FES\Lib\Http\Request::class,
        'params' => '',
    ],
    'view' => [
        'class' => \FES\Core\BaseView::class,
        'params' => '',
    ],
    'memc' => [
        'class' => \FES\Lib\Cache\MemCache::class,
        'params' => C('cache.memcached'),
    ]
];