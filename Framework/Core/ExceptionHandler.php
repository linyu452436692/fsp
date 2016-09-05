<?php
namespace FES\Core;
/**
 * @desc 系统有未捕获的异常抛出时的处理类
 * @author steven
 * @since 2015年8月19日
 */
class ExceptionHandler {
    public static function handler(\Exception $exception) {
        var_dump($exception);
    }
}

