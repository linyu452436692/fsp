<?php
namespace FES\Core;
/**
 * @desc 配置数据载入类
 * @author linyu@273.cn
 */
class Config {
    //配置数据集
    public static $data = [];
    
    /**
    * @desc 根据目录载入配置数据
     * @param string $dir 目录路径
     * @author linyu@273.cn 2015-5-13
     */
    public static function LoadByDir($dir) {
        $fileArr = self::files($dir);
        foreach ($fileArr as $key => $file) {
            self::loadByFile($file);
        }
    }
    
    /**
    * @desc 根据文件载入配置数据
     * @param string $file 文件路径
     * @author linyu@273.cn 2015-5-13
     */
    public static function loadByFile($file) {
        if (file_exists($file)) {
            $config = self::loadConfig($file);
            $filename = basename($file, '.php');
            self::$data[$filename] = !empty(self::$data[$filename]) ? array_merge(self::$data[$filename], $config) : $config;
        }
    }
    
    /**
    * @desc 根据数组载入配置数据
     * @param array $params 配置数据的数组
     * @author linyu@273.cn 2015-5-13
     */
    public static function loadByArr($params) {
        if (is_array($params)) {
            self::$data = array_merge(self::$data, $params);
        }
    }
    
    /**
     * 加载配置文件 支持格式转换 仅支持一级配置
     * @param string $file 配置文件名
     * @param string $parse 配置解析方法 有些格式需要用户自己解析
     * @return array
     */
    public static function loadConfig($file) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'php':
                return require $file;
            case 'ini':
                return parse_ini_file($file);
            case 'yaml':
                return yaml_parse_file($file);
            case 'xml':
                return (array) simplexml_load_file($file);
            case 'json':
                return json_decode(file_get_contents($file), true);
            default:
                throw new \Exception('Unsuported file type!');
        }
    }
    
    
    /**
     * @desc Get an array of all files in a directory.
     * @param string $directory
     * @return array
     */
    public static function files($directory) {
        $glob = glob($directory . '/*');
        if ($glob === false) return [];
        // To get the appropriate files, we'll simply glob the directory and filter
        // out any "files" that are not truly files so we do not end up with any
        // directories in our list, but only true files within the directory.
        return array_filter($glob, function ($file) {
            return filetype($file) == 'file';
        });
    }
}

