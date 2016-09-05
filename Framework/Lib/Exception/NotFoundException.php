<?php
namespace FES\Lib\Exception;
/**
 * @desc 没找到相关页面
 * @author linyu@273.cn
 * @since 2015年8月19日
 */
class NotFoundException extends \Exception {
    /**
     * Constructor.
     * @param string $message error message
     */
    public function __construct($message = null)
    {
        parent::__construct($message, empty(ExceptionCode::$code[__CLASS__]['code']) ? 0 : ExceptionCode::$code[__CLASS__]['code']);
    }
}

