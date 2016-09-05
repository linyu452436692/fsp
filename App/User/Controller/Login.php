<?php
namespace App\User\Controller;
use FES\Core\Controller;
class Login extends Controller {

    public function index() {
        $username = I('username');
        $model = new \App\User\Model\users();
        $ret = $model->getListAndCount([]);
        var_dump($ret);
        var_dump($model->getFields());
        $model->sqlLog(true);
    }

    public function doLogin() {
        
    }
}