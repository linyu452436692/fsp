<?php
namespace FES\Core;
/**
 * @desc 系统抛出错误时的处理类
 * @author steven
 * @since 2015年8月19日
 */
class ErrorHandler {
    public static function handler($errno, $errstr, $errfile, $errline) {
        echo $errno .$errstr . $errfile . $errline;
        die();
    }
}

