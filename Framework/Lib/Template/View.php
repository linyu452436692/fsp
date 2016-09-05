<?php
namespace FES\Lib\Template;
/**
 * @desc 视图引擎
 * @author steven
 * @since 2015-7-21
 */
class View {
    //模板变量
    protected $tVar = [];
    //视图所在目录
    public $tplDir = '';
    
    public function __construct($tplDir) {
        if (!is_dir($tplDir)) throw new \InvalidArgumentException('视图目录不存在[' . $tplDir . ']');
        $this->tplDir = $tplDir;
    }
    
    /**
     * @desc 给模板赋值
     * @param string $key 
     * @param mixed $val
     */
    public function assign($key, $val) {
        $this->$key = $val;
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
     * @desc 输出模板内容
     * @param string $content 模板内容
     * @author linyu@273.cn 2015-7-21
     */
    public function output($content) {
        echo $content;
    }
    
    /**
     * @desc 获取模板内容
     * @param string $tplFile 载入模板文件名，相对于视图路径指定
     * @param array $values 要传给模板的变量
     * @return string
     * @author linyu@273.cn 2015-7-21
     */
    public function fetch($tplFile, array $values = []) {
        // 页面缓存
        ob_start();
        ob_implicit_flush(0);
        // 模板阵列变量分解成为类独立变量
        $this->assignArr($values);
        //引入模板文件
        $this->_loadTpl($tplFile);
        // 获取并清空缓存
        $content = ob_get_clean();
        return $content;
    }
    
    /**
     * @desc 载入子模板，在模板中使用
     * @param string $tplFile 子模板文件名，相对于视图路径指定
     * @param array $values 要传给子模板的变量
     */
    protected function load($tplFile, array $values = []) {
        $this->assignArr($values);
        $this->_loadTpl($tplFile);
    }
    
    /**
     * @desc 载入模板文件
     * @param string $tplFile 载入模板文件名，相对于视图路径指定
     * @author linyu@273.cn 2015-7-21
     */
    private function _loadTpl($tplFile) {
        $tpl = $this->tplDir . $tplFile;
        if (is_file($tpl)) {
            include $tpl;
        } else {
            throw new \InvalidArgumentException('模块文件不存在[' . $tpl . ']');
        }
    }
}
