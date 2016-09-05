<?php
namespace FES\Core;
/**
 * @desc register_shutdown_function 系统运行结束时的处理类
 * @author steven
 * @since 2015年8月19日
 */
class ShutHandler {
    public static function handler() {
        if (!empty(C('RUN_BEGIN_TIME')) && TRUE  === C('base.debug')) {
            echo "<br>系统运行耗时：" . round((microtime(true) - C('RUN_BEGIN_TIME')) * 1000, 4) . " ms";
            echo "<br>系统占用内存：" . round(memory_get_usage() / 1024) . "KB";
        }
        $ret = error_get_last();
        if ($ret) {
            var_dump($ret);
        }
    }
}

