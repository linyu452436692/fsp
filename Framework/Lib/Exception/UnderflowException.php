<?php
namespace FES\Lib\Exception;
/**
 * @desc Exception thrown if a value does not match with a set of values. Typically this happens when a function calls another function and expects the return value to be of a certain type or value not including arithmetic or buffer related errors. 
 * @author linyu@273.cn
 * @since 2015年8月19日
 */
class UnexpectedValueException extends \RuntimeException {
    /**
     * Constructor.
     * @param string $message error message
     */
    public function __construct($message = null)
    {
        parent::__construct($message, empty(ExceptionCode::$code[__CLASS__]['code']) ? 0 : ExceptionCode::$code[__CLASS__]['code']);
    }
}

