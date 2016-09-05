<?php
namespace FES\Lib\Exception;
/**
 * @desc Exception thrown when performing an invalid operation on an empty container, such as removing an element. 
 * @author linyu@273.cn
 * @since 2015年8月19日
 */
class UnderflowException extends \RuntimeException {
    /**
     * Constructor.
     * @param string $message error message
     */
    public function __construct($message = null)
    {
        parent::__construct($message, empty(ExceptionCode::$code[__CLASS__]['code']) ? 0 : ExceptionCode::$code[__CLASS__]['code']);
    }
}

