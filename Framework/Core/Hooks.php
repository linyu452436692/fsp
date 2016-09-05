<?php
namespace FES\Core;
use FES\Lib\Exception\BadMethodCallException;
use FES\Lib\Exception\BadClassCallException;
use FES\Lib\Exception\BadFunctionCallException;
use FES\Lib\Exception\NotFoundException;
class Hooks {
    public static function call($params) {
        if (!empty($params['class'])) self::callClass($params['class']);
        if (!empty($params['function'])) self::callFunc($params['function']);
    }
    
    public static function callClass($classArr) {
        if (!empty($classArr['name'])) {
            if (!class_exists($classArr['name'])) throw new BadClassCallException('class ' . $classArr['name'] . ' not exists!');
            $class = new $classArr['name'];
            if (!method_exists($class, $classArr['action'])) throw new BadMethodCallException("{$class}->{$classArr['action']} doesn't exists.");
            if (!is_callable(array($class, $classArr['action']))) throw new BadMethodCallException("{$class}->{$classArr['action']} can't be callable.");
            if (empty($classArr['params'])) {
                $class->$classArr['action']();
            } else {
                $class->$classArr['action']($classArr['params']);
            }
        }
    }
    
    public static function callFunc($funcArr) {
        if (!empty($funcArr['file']) && !empty($funcArr['name'])) {
            if (!is_file($funcArr['file'])) throw new NotFoundException('function file ' . $funcArr['file'] . ' not exists!'); 
            require_once $funcArr['file'];
            if (!function_exists($funcArr['name'])) throw new BadFunctionCallException('function ' . $funcArr['name'] . ' not exists!');
            if (empty($funcArr['params'])) {
                $funcArr['name']();
            } else {
                $funcArr['name']($funcArr['params']);
            }
        }
    }
}
