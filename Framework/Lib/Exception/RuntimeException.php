<?php
namespace FES\Lib\Exception;
/**
 * @desc Exception thrown if an error which can only be found on runtime occurs. 
 * @author linyu@273.cn
 * @since 2015年8月19日
 */
class RuntimeException extends \Exception {
    /**
     * Constructor.
     * @param string $message error message
     */
    public function __construct($message = null)
    {
        parent::__construct($message, empty(ExceptionCode::$code[__CLASS__]['code']) ? 0 : ExceptionCode::$code[__CLASS__]['code']);
    }
}

