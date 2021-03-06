<?php
namespace FES\Lib\Exception;
/**
 * @desc Exception thrown if a value does not adhere to a defined valid data domain.
 * @author linyu@273.cn
 * @since 2015年11月13日
 */
class BadDomainCallException extends \LogicException {
    /**
     * Constructor.
     * @param string $message error message
     */
    public function __construct($message = null)
    {
        parent::__construct($message, empty(ExceptionCode::$code[__CLASS__]['code']) ? 0 : ExceptionCode::$code[__CLASS__]['code']);
    }
}

