<?php
require '../../config.php';
require LIBPATH.'/system/WebService.php';

$web = new WebService;

//设定可远程调用的客户端IP
$web->access_ip[] = '127.0.0.1';
$web->access_ip[] = '192.168.1.102';
//注册函数
$web->reg_func('testme','test');
//注册类
$web->reg_class('world','Foo');
//注册验证方式
$web->reg_auth('rpc_user_check');
//运行
$web->run();

/**
 * 检测用户是否有权限进行远程调用
 * @param $user
 * @param $pass
 * @return unknown_type
 */
function rpc_user_check($user,$getpass)
{
    //这里也可以换成查询数据库表的操作
    $passdb['test'] = '123456';

    //存在用户，而且密码正确
    $passhash = Auth::mkpasswd($user,$passdb[$user]);
    if(isset($passdb[$user]) and $passhash==$getpass) return true;
    else return false;
}

function test($name)
{
    return array('hello','world!');
}

class Foo
{
    public $index;

    function getinfo($param)
    {
        return 'my index is '.$this->index.'; param :'.$param;
    }
}