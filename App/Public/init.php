<?php
//常量风格配置文件
return [
    'APP_NAME' => 'App', //项目名称
    'ENV_CONF_PATH' => APP_PATH . 'Conf/' . ENV, //配置文件目录
    'COMMON_CONF_PATH' => APP_PATH . 'Conf/Common', //通用配置文件目录
    'URL_RULE' => 1, //0.query_string模式1.pathinfo模式 2.命令行模式
    'MODULE' => 'Index', //默认请求的模块m
    'CONTROLLER' => 'Index', //默认请求的控制器c
    'ACTION' => 'index', //默认请求的操作a
    'SERVICE_DIRNAME' => 'Service', //服务接口目录名
    'MODEL_DIRNAME' => 'Model', //数据模型目录名
    'CONTROLLER_DIRNAME' => 'Controller', //控制器目录名
    'VIEW_DIRNAME' => 'View', //视图目录名
    'TIMEZONE' => 'Asia/Shanghai', //时区
    'HEADERS' => [
        'Access-Control-Allow-Origin' => ['http://www.baidu.com/', 'http://www.273.cn/'], //ajax允许的跨域请求域名
    ],
];