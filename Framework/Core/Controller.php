<?php
namespace FES\Core;
use FES\Lib\Exception\BadClassCallException;
/**
 * Controller is the base class of web controllers.
 */
class Controller {
    public $model = null;
    /**
     * Redirects the browser to the specified URL.
     */
    public function redirect($url, $statusCode = 302, $checkAjax = false) {
        return C('util')->response->redirect($url, $statusCode, $checkAjax);
    }

    /**
     * Redirects the browser to the last visited page.
     */
    public function goBack($defaultUrl = null) {}

    /**
     * Refreshes the current page.
     */
    public function refresh($anchor = '') {
        return C('util')->response->refresh($anchor);
    }
    
    /**
     * return the json data.
     */
    public function jsonReturn(array $data = []) {
        C('util')->response->content = json_encode($data);
        C('util')->response->send();
        exit();
    }
    
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
