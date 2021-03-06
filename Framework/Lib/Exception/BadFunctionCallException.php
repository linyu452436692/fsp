<?php
namespace FES\Lib\Exception;
/**
 * @desc Exception thrown if a callback refers to an undefined function or if some arguments are missing. 
 * @author linyu@273.cn
 * @since 2015年11月13日
 */
class BadFunctionCallException extends \LogicException {
    /**
     * Constructor.
     * @param string $message error message
     */
    public function __construct($message = null)
    {
        parent::__construct($message, empty(ExceptionCode::$code[__CLASS__]['code']) ? 0 : ExceptionCode::$code[__CLASS__]['code']);
    }
}

