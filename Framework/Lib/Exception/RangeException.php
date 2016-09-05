<?php
namespace FES\Lib\Exception;
/**
 * @desc Exception thrown to indicate range errors during program execution. Normally this means there was an arithmetic error other than under/overflow. This is the runtime version of DomainException.  
 * @author linyu@273.cn
 * @since 2015年8月19日
 */
class RangeException extends \RuntimeException {
    /**
     * Constructor.
     * @param string $message error message
     */
    public function __construct($message = null)
    {
        parent::__construct($message, empty(ExceptionCode::$code[__CLASS__]['code']) ? 0 : ExceptionCode::$code[__CLASS__]['code']);
    }
}

