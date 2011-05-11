<?php
class test extends Controller
{
    function index()
    {
        //session();
        $data = createModel('UserInfo')->get(12)->get();
        $this->swoole->tpl->assign('data',$data);
        $html = $this->swoole->tpl->fetch('test.html');
        $time = $this->showTime();
        return $html.$time;
    }

    function sess()
    {
        $this->session_start();
        //$_SESSION['hello'] = "write to session";
        return $_SESSION['hello'].$this->showTime();
    }
}