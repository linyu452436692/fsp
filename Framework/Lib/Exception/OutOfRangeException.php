<?php
namespace FES\Lib\Exception;
/**
 * @desc Exception thrown when an illegal index was requested. This represents errors that should be detected at compile time. 
 * @author linyu@273.cn
 * @since 2015年11月13日
 */
class OutOfRangeException extends \LogicException {
    /**
     * Constructor.
     * @param string $message error message
     */
    public function __construct($message = null)
    {
        parent::__construct($message, empty(ExceptionCode::$code[__CLASS__]['code']) ? 0 : ExceptionCode::$code[__CLASS__]['code']);
    }
}

