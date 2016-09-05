<?php
namespace FES\Lib\Exception;
/**
 * @desc Exception that represents error in the program logic. This kind of exception should lead directly to a fix in your code. 
 * @author linyu@273.cn
 * @since 2015年8月19日
 */
class LogicException extends \Exception {
    /**
     * Constructor.
     * @param string $message error message
     */
    public function __construct($message = null)
    {
        parent::__construct($message, empty(ExceptionCode::$code[__CLASS__]['code']) ? 0 : ExceptionCode::$code[__CLASS__]['code']);
    }
}

