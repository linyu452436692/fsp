<?php

/**
 * 获取输入参数
 * @param string $name 输入参数的名称(以.来区分请求类型，.前面为请求类型后面有参数名,如post.id)
 * @param mixed $default 不存在的时候默认值
 * @return mixed
 */
function I($name, $default = null) {
    $type = '';
    if (strpos('.', $name)) {
        $methodArr = explode('.', $name);
        $type = strtoupper($methodArr[0]);
    }
    switch ($type) {
        case 'GET':
            $value = isset($_GET[$name]) ? $_GET[$name] : $default;
            break;
        case 'POST':
            $value = isset($_POST[$name]) ? $_POST[$name] : $default;
            break;
        case 'REQUEST':
            $value = isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
            break;
        default:
            $value = isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : $default);
            break;
    }
    return rawurldecode(strip_tags($value));
}

/**
 * 获取和设置配置参数
 * @param string $name 配置变量(.号来分隔数组层次)
 * @param mixed $value 配置值
 * @return mixed
 */
function C($name = NULL, $value = NULL) {
    if (NULL === $name) {
        return \FES\Core\Config::$data;
    }
    return array_gos($name, \FES\Core\Config::$data, $value);
}

/**
 * 获取和设置对应的语言内容
 * @param string|array $name 语言变量
 * @param mixed $value 语言值或者变量k
 * @return mixed
 */
function L($name = NULL, $value = NULL) {
    static $_lang = [];
    if (NULL === $name) {
        return $_lang;
    }
    if (NULL === $value) {
        return isset($_lang[$name]) ? $_lang[$name] : NULL;
    } else {
        $_lang[$name] = $value;
    }
    return TRUE;
}

/**
 * @desc 根据参数获取或设置数组里的值
 * @param string $key 参数
 * @param array $arr 数组
 * @param string $value 要设置的值
 * @param string $join 参数的连接方式
 * @return mixed
 * @author linyu@273.cn 2015年8月17日
 */
function array_gos($key, &$arr, $value = NULL, $join = '.') {
    if (strpos($key, $join)) {
        $keyArr = explode($join, $key);
        $k = $keyArr[0];
        $arr[$k] = isset($arr[$k]) ? $arr[$k] : NULL;
        array_shift($keyArr);
        $key = implode($join, $keyArr);
        return array_gos($key, $arr[$k], $value);
    } else {
        if (NULL === $value) {
            return isset($arr[$key]) ? $arr[$key] : NULL;
        } else {
            if (!is_array($arr)) $arr = [];
            $arr[$key] = $value;
            return true;
        }
    }
}

/**
 * 字符串命名风格转换 type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
 * @param string $name 字符串
 * @param integer $type 转换类型
 * @return string
 */
function parse_name($name, $type = 0) {
    if ($type) {
        return ucfirst(preg_replace_callback('/_([a-zA-Z])/', function ($match) {
            return strtoupper($match[1]);
        }, $name));
    } else {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}
