<?php
namespace FES\Core;
use Illuminate\Database\Capsule\Manager as Capsule;
use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;
use FES\Lib\Exception\InvalidArgumentException;
class Bootstrap {
    public $config = [];
    
    public function __construct(array $config = []) {
        $this->config = $config;
    }
    
    //运行系统
    public function run() {
        //初始化
        $this->_init();
        //解析路由
        Dispatcher::parseRoute();
        //进行分发
        Dispatcher::dispatch();
    }
    
    //初始化系统
    private function _init() {
        //是否命令行模式
        C('CLI', (PHP_SAPI == 'cli') ? TRUE : FALSE);
        //错误级别
        $this->_setErrorLevel();
        //错误处理
        $this->_setErrorHandler();
        //载入配置文件
        !empty($this->config) && Config::loadByArr($this->config);
        Config::LoadByDir(C('ENV_CONF_PATH'));
        Config::LoadByDir(C('COMMON_CONF_PATH'));
        //初始化单例插件库
        $this->_initUtil();
        //初始化数据库连接
        $this->_initDatabase();
        //初始化系统环境
        $this->_initEnv();
    }
    
    //设置错误处理方式
    private function _setErrorHandler() {
        set_error_handler(['FES\\Core\\ErrorHandler', 'handler'], E_USER_ERROR);
        set_exception_handler(['FES\\Core\\ExceptionHandler', 'handler']);
        register_shutdown_function(['FES\\Core\\ShutHandler', 'handler']);
        if (ENV == 'Local' && !C('CRONTAB')) {
            //filp/whoops 代码错误提示插件
            $run = new Run();
            $run->pushHandler(new PrettyPageHandler());
            $jsonHandler = new JsonResponseHandler();
            $jsonHandler->onlyForAjaxRequests(true);
            $run->pushHandler($jsonHandler);
            $run->register();
        }
    }
    
    //设置错误级别
    private function _setErrorLevel() {
        switch (ENV) {
            case 'Online':
                ini_set('display_errors', 0);
                if (version_compare(PHP_VERSION, '5.3', '>=')) {
                    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
                } else {
                    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
                }
                break;
            case 'Sim':
            case 'Test':
            case 'Local':
                error_reporting(-1);
                ini_set('display_errors', 1);
                break;
            default:
                header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
                exit('The application environment is not set correctly.');
        }
    }
    
    //初始化单例插件库slime container
    private function _initUtil() {
        $di = new Container();
        if (!empty(C('components'))) {
            foreach (C('components') as $key => $val) {
                if (empty($val['class'])) throw new \Exception('illegal components');
                $di->singleton($key, function () use ($val) {
                    return new $val['class'](isset($val['params']) ? $val['params'] : '');
                });
            }
        }
        C('util', $di);
    }
    
    //初始化数据库连接laravel eloquent orm
    private function _initDatabase() {
        $capsule = new Capsule();
        if (!empty(C('db'))) {
            foreach (C('db') as $key => $val) {
                $capsule->addConnection(C('db.' . $key), $key);
            }
        } else {
            throw new InvalidArgumentException("C('db') is empty!");
        }
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
    
    //初始化系统环境
    private function _initEnv() {
        //设置时区
        ini_set('date.timezone', C('TIMEZONE'));
        //
        header("Content-type: text/html; charset=utf-8");
        //兼容ajax跨域
        if (!empty($_SERVER['HTTP_REFERER'])) {
            preg_match('/^http:\/\/[\w\.]+\//', $_SERVER['HTTP_REFERER'], $matches);
            if (!empty($matches)) {
                $domain = $matches[0];
                if (!empty(C('HEADERS')['Access-Control-Allow-Origin']) && in_array($domain, C('HEADERS')['Access-Control-Allow-Origin'])) {
                    header('Access-Control-Allow-Origin: ' . $domain);
                }
            }
        }
        //初始化环境钩子
        Hooks::call(C('hooks.init_env'));
    }
}
