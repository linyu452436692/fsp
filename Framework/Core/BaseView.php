<?php
namespace FES\Core;
/**
 * @desc 视图页面基类
 * @author steven
 * @since 2015-7-21
 */
class BaseView {
    // 模板引擎
    protected $view;
    
    public function __construct() {
        $this->view = new \FES\Lib\Template\View(APP_PATH . C('MODULE') . '/' . C('VIEW_DIRNAME') . '/');
    }
    
    /**
     * @desc 设置视图所在目录
     * @param string $tplDir 视图目录 
     * @author linyu@273.cn 2015-7-21
     */
    public function setTplDir($tplDir) {
        $this->view->tplDir = $tplDir;
    }

    /**
     * @desc 给模板赋值
     * @param string $key
     * @param mixed $val
     * @author linyu@273.cn 2015-7-21
     */
    public function assign($key, $val) {
        $this->view->assign($key, $val);
    }
    
    /**
     * @desc 给模板赋值
     * @param array $values 要传给模板的变量
     * @author linyu@273.cn 2015-7-21
     */
    public function assignArr(array $values) {
        if (!empty($values)) {
            foreach ($values as $key => $val) {
                $this->assign($key, $val);
            }
        }
    }

    /**
     * @desc 渲染输出模板页面
     * @param array $data 要传给模板的变量
     * @param string $tpl 模板相对视图路径
     * @author linyu@273.cn 2015-7-21
     */
    public function render($data, $tpl = '') {
        $content = $this->view->fetch($this->_getTpl($tpl), $data);
        $this->view->output($content);
        exit();
    }
    
    /**
     * @desc 输出模板页面
     * @param string $tpl 模板相对视图路径
     * @author linyu@273.cn 2015-7-21
     */
    public function display($tpl = '') {
        $content = $this->view->fetch($this->_getTpl($tpl));
        $this->view->output($content);
        exit();
    }
    
    /**
     * @desc 获取模板内容
     * @param string $tplFile 载入模板文件名，相对于视图路径指定
     * @param array $values 要传给模板的变量
     * @return string
     * @author linyu@273.cn 2015-08-10
     */
    public function fetch($tplFile, array $values = []) {
        $content = $this->view->fetch($this->_getTpl($tplFile));
        return $content;
    }
    
    /**
     * @desc 生成模板文件名
     * @return string
     * @author linyu@273.cn 2015-7-21
     */
    private function _getTpl($tpl) {
        if (empty($tpl)) {
            return $this->parseName(C('ACTION')) . '.php';
        } else {
            return (substr($tpl, -4) == '.php') ? $tpl : $tpl . '.php';
        }
    }

   /**
    * 字符串命名风格转换
    * @param string $name 字符串
    * @param integer $type 转换类型（type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格）
    * @return string
    */
    public function parseName($name, $type = 0) {
        if ($type) {
            return ucfirst(preg_replace("/_([a-zA-Z])/e", "strtoupper('\\1')", $name));
        } else {
            return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
        }
    }
}
