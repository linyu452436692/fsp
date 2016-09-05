<?php
namespace FES\Lib\Exception;
/**
 * @desc Exception thrown when adding an element to a full container. 
 * @author linyu@273.cn
 * @since 2015年8月19日
 */
class OverflowException extends \RuntimeException {
    /**
     * Constructor.
     * @param string $message error message
     */
    public function __construct($message = null)
    {
        parent::__construct($message, empty(ExceptionCode::$code[__CLASS__]['code']) ? 0 : ExceptionCode::$code[__CLASS__]['code']);
    }
}

