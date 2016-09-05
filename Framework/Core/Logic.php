<?php
namespace FES\Core;
use FES\Lib\Exception\BadClassCallException;
/**
 * Logic is the base class of web Logic.
 */
class Logic {
    public $model = null;
    
    /**
     * Property Overloading
     */
    public function __get($key) {
        if (preg_match('/model$/i', $key)) {
            if (isset($this->model[$key])) return $this->model[$key];
            $className = preg_replace('/model$/i', '', $key);
            $classSpace = '\\' . C('APP_NAME') . '\\' . C('MODULE') . '\\' . C('MODEL_DIRNAME') . '\\';
            $class = $classSpace . $className;
            if (!class_exists($class)) throw new BadClassCallException("{$class} doesn't exists.");
            $this->model[$key] = new $class();
            return $this->model[$key];
        }
    }
}
