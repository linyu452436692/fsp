<?php
namespace App\User\Controller;
use FES\Core\Controller;
use FES\Core\Session;
class Index extends Controller {

    public function index() {
        echo 'This is a index page';
    }

    public function test() {
        $this->jsonReturn(['1' => 'test']);
    }
    
    public function show() {
        C('util')->view->assign('name', 'steven');
        C('util')->view->display();
    }
    
    public function test2() {
        $ret = $this->usersModel->getListAndCount([]);
        var_dump($ret);
        var_dump($this->usersModel->getFields());
        $this->usersModel->sqlLog(true);
    }
    
    public function test3() {
        session_start();
        var_dump($_COOKIE);
        var_dump($_SESSION);
        echo session_id();
    }
    
    public function test4() {
        $session = new Session(C('session'));
        var_dump($_COOKIE);
        var_dump($_SESSION);
        echo session_id();
        $_SESSION['test2'] = 2;
    }
    
    public function test5() {
        $a = C('util')->memc->get('0baaaea27fbd2c95c9ff5f82d623377913dbca7e');
        var_dump(unserialize($a));
    }
    
    public function test6() {
        $ret = $this->usersModel->createConditions(['where' => [['id', '=', 1]]])->get()->toArray();
        //var_dump($ret);
        $ret = $this->testModel->createConditions(['where' => [['id', '=', 2]]])->get()->toArray();
        //var_dump($ret);
        $ret = $this->usersModel->createConditions(['where' => [['id', '=', 3]]])->get()->toArray();
        //var_dump($ret);
        $this->testModel->sqlLog();
    }
    
    public function test7() {
        $ary = [];
        $ary[0] = 'a';
        $ary[1] = 'b';
        echo json_encode($ary) . '<br>';
        echo json_encode($ary, JSON_FORCE_OBJECT);
    }

    public function phpinfo() {
        phpinfo();
    }
}
