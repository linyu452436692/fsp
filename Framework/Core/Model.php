<?php
namespace FES\Core;
use Illuminate\Database\Capsule\Manager as Capsule;
class Model extends \Illuminate\Database\Eloquent\Model {
    //原始数据模型对象
    protected $origin = null;
    //数据模型对象
    protected $model = null;
    
    public function __construct() {
        $this->model = $this->origin = $this;
        //开启日志
        if (TRUE === C('base.debug')) {
            Capsule::connection($this->connection)->enableQueryLog();
        }
        parent::__construct();
    }
    
    /**
     * 获取最后一条sql语句
     * @return mixed
     */
    public function lastSql($isOutput = true) {
        $sql = end($this->sqlLog(false));
        if ($isOutput) {
            var_dump($sql);
        } else {
            return $sql;
        }
    }

    /**
     * 获取一组sql语句
     * @param bool $isOutput
     * @return array
     */
    public function sqlLog($isOutput = true) {
        $sqlLogArr = Capsule::connection($this->connection)->getQueryLog();
        if ($isOutput) {
            var_dump($sqlLogArr);
        } else {
            return $sqlLogArr;
        }
    }
    
    public function querySql($sql) {
        $result = Capsule::connection($this->connection)->select($sql);
        return json_decode(json_encode($result), true);
    }
    
    public function getFields() {
        $result = $this->querySql('SHOW FULL COLUMNS FROM ' . $this->table);
        $info = [];
        if($result) {
            foreach ($result as $key => $val) {
                //transfer int(10) => int
                $val['type'] = preg_replace('/\([^\(\)]+\)\s?\w*/', '', $val['Type']);
                $info[] = $val;
            }
        }
        return $info;
    }
    
    public function add($insertArr) {
        return !empty($insertArr) ? $this->model->insertGetId($insertArr) : false;
    }
    
    public function edit($conditions, $updateArr) {
        if (empty($conditions)) return false;
        $this->createConditions($conditions);
        return $this->model->update($updateArr);
    }
    
    public function del($params) {
        $this->createConditions($params)->delete();
    }
    
    public function getList($params) {
        $list = $this->createConditions($params)->get()->toArray();
        return $list;
    }
    
    public function getListAndCount($params) {
        $this->createConditions($params);
        $list = $this->model->get()->toArray();
        $count = $this->model->forPage(1, 1)->count();
        return [
            'list' => $list,
            'total' => $count,
        ];
    }
    
    /**
     * @desc 无则新增，有则更新
     * @param array $params 参数数组
     * @return boolean
     * @author steven 2015-6-24
     */
    public function editOrNew($params) {
        if (empty($params['username'])) return false;
        $model = $this->model->find($params['username']);
        if (!empty($model)) {
            foreach ($params as $key => $val) {
                if (isset($model[$key])) $model->$key = $val;
            }
            return $model->save();
        } else {
            return $this->add($params);
        }
    }
    
    /**
     * 拼接查询语句
     * @param array $params
     * @author steven 2015-5-20
     * @example $conditions = array(
     * 'where' => array( array('test', '=', 1), array('test2', '>', 1) ),
     * 'orWhere' => array( array('test', '=', 1), 'or' => array('test2', '>', 1) ),
     * 'whereOr' => array( array('test', '=', 1), 'or' => array('test2', '>', 1) ),
     * 'order' => array('create_time' => 'desc', 'id' => 'desc'),
     * 'limit' => array($page, $pageSize)
     * );
     */
    public function createConditions($params) {
        //$this->model = $this->origin;
        if (empty($params['limit'])) {
            $params['limit'] = [1, 2000];
        }
        foreach ($params as $key => $val) {
            switch (strtolower($key)) {
                case 'where':
                    $this->_createWhere($val);
                    break;
                case 'orwhere':
                    $this->_createOrWhere($val);
                    break;
                case 'whereor':
                    $this->_createWhereOr($val);
                    break;
                case 'order':
                    $this->_createOrder($val);
                    break;
                case 'limit':
                    $this->_createLimit($val);
                    break;
                default:
                    break;
            }
        }
        return $this->model;
    }
    
    /**
     * @desc 根据条件拼接查询语句where
     * @param array $params
     * @author steven 2015-5-21
     * @example
     *  array( array('test', '=', 1), array('test2', '>', 1) ) => $this->model = $this->model->where('test', '=', 1)->where('test2', '>', 1);
     */
    protected function _createWhere($params) {
        if (empty($params)) return $this->model;
        foreach ($params as $key => $val) {
            switch (strtolower($val[1])) {
                case '=':
                case '>':
                case '>=':
                case '<':
                case '<=':
                case '!=':
                case 'like':
                    $this->model = $this->model->where($val[0], $val[1], $val[2]);
                    break;
                case 'in':
                    $this->model = $this->model->whereIn($val[0], $val[2]);
                    break;
                case 'not in':
                    $this->model = $this->model->whereNotIn($val[0], $val[2]);
                    break;
                default:
                    break;
            }
        }
        return $this->model;
    }
    
    /**
     * @desc 根据条件拼接查询语句where前置or查询
     * @param array $params
     * @author steven 2015-5-21
     * @example
     *  array(
     *   array('test', '=', 1),
     *   'or' => array('test2', '>', 1)
     *  )
     *  => $this->model = $this->model->orWhere(function($query) use ($params) { $query->where('test', '=', 1)->orWhere('test2', '>', 1);});
     *  => sql: or (`test` = 1 or `test` > 1)
     */
    protected function _createOrWhere($params) {
        if (empty($params)) return $this->model;
        $this->model = $this->model->orWhere(function($query) use ($params) {
            foreach ($params as $key => $val) {
                if (is_array($val)) {
                    if (strtolower($key) == 'or') {
                        switch (strtolower($val[1])) {
                            case '=':
                            case '>':
                            case '>=':
                            case '<':
                            case '<=':
                            case '!=':
                            case 'like':
                                $query = $query->orWhere($val[0], $val[1], $val[2]);
                                break;
                            case 'in':
                                $query = $query->orWhereIn($val[0], $val[2]);
                                break;
                            case 'not in':
                                $query = $query->orWhereNotIn($val[0], $val[2]);
                                break;
                            default:
                                break;
                        }
                    } else {
                        switch (strtolower($val[1])) {
                            case '=':
                            case '>':
                            case '>=':
                            case '<':
                            case '<=':
                            case '!=':
                            case 'like':
                                $query = $query->orWhere($val[0], $val[1], $val[2]);
                                break;
                            case 'in':
                                $query = $query->orWhereIn($val[0], $val[2]);
                                break;
                            case 'not in':
                                $query = $query->orWhereNotIn($val[0], $val[2]);
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
        });
        return $this->model;
    }
    
    /**
     * @desc 根据条件拼接查询语句where内置or查询
     * @param array $params
     * @author steven 2015-5-21
     * @example
     *  array(
     *   array('test', '=', 1),
     *   'or' => array('test2', '>', 1)
     *  )
     *  => $this->model = $this->model->where(function($query) use ($params) { $query->where('test', '=', 1)->orWhere('test2', '>', 1);});
     *  => sql: and (`test` = 1 or `test` > 1)
     */
    protected function _createWhereOr($params) {
        if (empty($params)) return $this->model;
        $this->model = $this->model->where(function($query) use ($params) {
            foreach ($params as $key => $val) {
                if (is_array($val)) {
                    if (strtolower($key) == 'or') {
                        switch (strtolower($val[1])) {
                            case '=':
                            case '>':
                            case '>=':
                            case '<':
                            case '<=':
                            case '!=':
                            case 'like':
                                $query = $query->orWhere($val[0], $val[1], $val[2]);
                                break;
                            case 'in':
                                $query = $query->orWhereIn($val[0], $val[2]);
                                break;
                            case 'not in':
                                $query = $query->orWhereNotIn($val[0], $val[2]);
                                break;
                            default:
                                break;
                        }
                    } else {
                        switch (strtolower($val[1])) {
                            case '=':
                            case '>':
                            case '>=':
                            case '<':
                            case '<=':
                            case '!=':
                            case 'like':
                                $query = $query->orWhere($val[0], $val[1], $val[2]);
                                break;
                            case 'in':
                                $query = $query->orWhereIn($val[0], $val[2]);
                                break;
                            case 'not in':
                                $query = $query->orWhereNotIn($val[0], $val[2]);
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
        });
        return $this->model;
    }
    
    /**
     * @desc 根据条件拼接查询语句order by
     * @param array $params
     * @author steven 2015-5-21
     * @example
     *  array('create_time' => 'desc', 'id' => 'desc') => $this->model = $this->model->orderBy('create_time', 'desc')->orderBy('id', 'desc');
     */
    protected function _createOrder($params) {
        if (empty($params)) return $this->model;
        foreach ($params as $key => $val) {
            $this->model = $this->model->orderBy($key, $val);
        }
        return $this->model;
    }
    
    /**
     * @desc 根据条件拼接查询语句分页部分
     * @param array $params
     * @author steven 2015-5-21
     * @example
     *  array(1, 10) => $this->model = $this->model->forPage(1, 10);
     */
    protected function _createLimit($params) {
        if (empty($params)) return $this->model;
        $this->model = $this->model->forPage($params[0], $params[1]);
        return $this->model;
    }
}
