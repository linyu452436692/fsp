<?php
namespace FES\Lib\Exception;
/**
 * @desc Exception thrown if a value is not a valid key. This represents errors that cannot be detected at compile time. 
 * @author linyu@273.cn
 * @since 2015年8月19日
 */
class OutOfBoundsException extends \RuntimeException {
    /**
     * Constructor.
     * @param string $message error message
     */
    public function __construct($message = null)
    {
        parent::__construct($message, empty(ExceptionCode::$code[__CLASS__]['code']) ? 0 : ExceptionCode::$code[__CLASS__]['code']);
    }
}

