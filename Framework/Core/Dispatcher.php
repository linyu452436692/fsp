<?php
namespace FES\Core;
use FES\Lib\Exception\BadMethodCallException;
use FES\Lib\Exception\BadClassCallException;
class Dispatcher {
    //解析路由(0.query_string模式1.pathinfo模式 2.命令行模式)
    public static function parseRoute() {
        if (C('CLI')) C('URL_RULE', 2);
        switch (C('URL_RULE')) {
            case 1:
                $pathInfo = trim(str_replace('//', '/', C('util')->request->getPathInfo()), '/');
                $ary = !empty($pathInfo) ? explode('/', $pathInfo) : [];
                !empty($ary) && C('MODULE', array_shift($ary));
                !empty($ary) && C('CONTROLLER', array_shift($ary));
                !empty($ary) && C('ACTION', array_shift($ary));
                $urlParam = [];
                preg_replace_callback('/(\w+)\/([^\/]+)/', function($match) use(&$urlParam){$urlParam[$match[1]]=$match[2];}, implode('/', $ary));
                $_GET = array_merge($_GET, $urlParam);
                break;
            case 2:
                $opts = getopt("m:c:a:");
                !empty($opts['m']) && C('MODULE', $opts['m']);
                !empty($opts['c']) && C('CONTROLLER', $opts['c']);
                !empty($opts['a']) && C('ACTION', $opts['a']);
                break;
            case 0:
            default:
                C('MODULE', I('m', null));
                C('CONTROLLER', I('c', null));
                C('ACTION', I('a', null));
                break;
        }
    }
    
    //分发
    public static function dispatch() {
        $controllerPath = str_replace('/', '\\', trim(C('CONTROLLER_DIRNAME'), '/'));
        $classSpace = '\\' . C('APP_NAME') . '\\' . C('MODULE') . '\\' . $controllerPath . '\\';
        $className = $classSpace . C('CONTROLLER');
        $action = C('ACTION');
        if (!class_exists($className)) throw new BadClassCallException("{$className} doesn't exists.");
        $controller = new $className;
        if (!method_exists($controller, $action)) throw new BadMethodCallException("{$className}->{$action} doesn't exists.");
        if (!is_callable(array($controller, $action))) throw new BadMethodCallException("{$className}->{$action} can't be callable.");
        //执行前判断hooks
        Hooks::call(C('hooks.pre_action'));
        //执行操作 
        $controller->$action();
    }
    
    //api分发
    public static function dispatchApi() {
        $servicePath = str_replace('/', '\\', trim(C('SERVICE_DIRNAME'), '/'));
        $classSpace = '\\' . C('APP_NAME') . '\\' . $controllerPath . '\\' . C('MODULE') . '\\';
        $className = $classSpace . C('CONTROLLER');
        $action = C('ACTION');
        if (!class_exists($className)) throw new BadClassCallException("{$className} doesn't exists.");
        $controller = new $className;
        if (!method_exists($controller, $action)) throw new BadMethodCallException("{$className}->{$action} doesn't exists.");
        if (!is_callable(array($controller, $action))) throw new BadMethodCallException("{$className}->{$action} can't be callable.");
        //执行前判断hooks
        Hooks::call(C('hooks.pre_api'));
        //执行操作
        $controller->$action();
    }
}
